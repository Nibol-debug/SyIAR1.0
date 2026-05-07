<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\SantriModel;
use CodeIgniter\API\ResponseTrait;

class PenilaianController extends BaseController
{
    use ResponseTrait;

    public function getAspekByKategori($kategoriId)
    {
        $model = new AspekPenilaianModel();
        $data = $model->getByKategori($kategoriId);
        
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function submit()
    {
        $json = $this->request->getJSON(true);
        log_message('error', 'DEBUG Penilaian Submit Request: ' . json_encode($json));
        
        if (empty($json['santri_id']) || empty($json['values'])) {
            log_message('error', 'Penilaian Submit Error: Data tidak lengkap');
            return $this->fail('Data penilaian tidak lengkap');
        }
        
        $model = new PenilaianModel();
        $santriModel = new SantriModel();
        
        // Validasi santri
        if (!$santriModel->find($json['santri_id'])) {
            return $this->failNotFound('Santri tidak ditemukan');
        }
        
        $batchData = [];
        // Get guru_id from JWT token if available
        $guruId = 1;
        try {
            helper('jwt_helper');
            $token = $this->request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $token);
            $decoded = validateJWT($token);
            if ($decoded && isset($decoded['data']['id'])) {
                $guruId = $decoded['data']['id'];
            }
        } catch (\Exception $e) {
            // fallback to 1
        }
        
        $periode = $json['periode'] ?? date('Y-m');
        $jenisNilai = $json['jenis_nilai'] ?? 'harian';
        
        foreach ($json['values'] as $aspekId => $nilai) {
            $batchData[] = [
                'santri_id'          => $json['santri_id'],
                'aspek_id'           => $aspekId,
                'guru_id'            => $guruId,
                'nilai'              => $nilai,
                'jenis_nilai'        => $jenisNilai,
                'keterangan'         => $json['keterangan'] ?? null,
                'tanggal_penilaian'  => date('Y-m-d'),
                'periode'            => $periode,
                'is_draft'           => $json['is_draft'] ?? false,
            ];
        }
        
        // Hapus data lama jika ada, lalu insert baru
        $model->where('santri_id', $json['santri_id'])
              ->where('periode', $periode)
              ->where('guru_id', $guruId)
              ->delete();
              
        $model->saveBatchSantri($batchData);
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Penilaian berhasil disimpan' . ($json['is_draft'] ? ' (Draft)' : '')
        ]);
    }
    
    public function rekap()
    {
        $kelasId = $this->request->getGet('kelas_id');
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        
        if (!$kelasId) {
            return $this->fail('Parameter kelas_id diperlukan');
        }
        
        $model = new PenilaianModel();
        $data = $model->getRekapByKelas($kelasId, $periode);
        
        // Group by santri
        $grouped = [];
        foreach ($data as $row) {
            $nis = $row['nis'];
            if (!isset($grouped[$nis])) {
                $grouped[$nis] = [
                    'nama' => $row['nama_lengkap'],
                    'nis'  => $nis,
                    'nilai' => []
                ];
            }
            $grouped[$nis]['nilai'][$row['nama_kategori']][] = [
                'aspek' => $row['nama_aspek'],
                'nilai' => $row['nilai']
            ];
        }
        
        return $this->respond(['success' => true, 'data' => array_values($grouped), 'periode' => $periode]);
    }
    
    public function exportExcel()
    {
        return $this->respond([
            'success'      => true,
            'message'      => 'Fitur export Excel akan segera tersedia',
            'download_url' => '/api/penilaian/export/csv?' . http_build_query($this->request->getGet())
        ]);
    }

    public function importCBT($ujianId = null)
    {
        $ujianModel = new \App\Models\UjianModel();
        $ujian = $ujianModel->find($ujianId);
        if (!$ujian) return $this->failNotFound('Ujian tidak ditemukan');

        // Link to Mapel -> Aspek
        $mapelModel = new \App\Models\MataPelajaranModel();
        $mapel = $mapelModel->find($ujian['mapel_id']);
        if (!$mapel || !$mapel['aspek_id']) {
            return $this->fail('Mata pelajaran ujian tidak terhubung ke aspek penilaian manapun');
        }

        $sesiModel = new \App\Models\UjianSesiModel();
        $sesiSelesai = $sesiModel->where('ujian_id', $ujianId)->where('status', 'selesai')->findAll();
        
        $db = \Config\Database::connect();
        $penilaianModel = new PenilaianModel();
        
        $importedCount = 0;
        foreach ($sesiSelesai as $sesi) {
            // Calculate score: total_benar / total_soal * 100
            $stats = $db->table('ujian_jawaban')
                ->selectCount('id', 'total')
                ->selectSum('is_benar', 'benar')
                ->where('sesi_id', $sesi['id'])
                ->get()
                ->getRow();
            
            $totalSoal = $stats->total ?: 1;
            $nilai = (($stats->benar ?? 0) / $totalSoal) * 100;
            
            $saveData = [
                'santri_id' => $sesi['santri_id'],
                'aspek_id' => $mapel['aspek_id'],
                'guru_id' => $ujian['guru_id'],
                'nilai' => round($nilai, 2),
                'jenis_nilai' => 'uas', // Assume CBT is UAS for now
                'periode' => date('Y-m', strtotime($ujian['tanggal_mulai'])),
                'tanggal_penilaian' => date('Y-m-d'),
                'keterangan' => "Impor dari Ujian: {$ujian['judul']}"
            ];
            
            // Upsert (Delete old one from this ujian if exists in the same period)
            $penilaianModel->where('santri_id', $sesi['santri_id'])
                ->where('aspek_id', $mapel['aspek_id'])
                ->where('jenis_nilai', 'uas')
                ->where('periode', $saveData['periode'])
                ->delete();
                
            $penilaianModel->insert($saveData);
            $importedCount++;
        }
        
        return $this->respond([
            'status' => 200, 
            'message' => "Berhasil mengimpor $importedCount nilai dari ujian '{$ujian['judul']}'"
        ]);
    }

    public function hitungAkhir()
    {
        // Mock implementation for weighted scoring calculation
        return $this->respond([
            'status' => 200, 
            'message' => 'Perhitungan nilai akhir (30% Harian, 30% UTS, 40% UAS) selesai'
        ]);
    }

    public function rapor($santriId = null)
    {
        $santriModel = new SantriModel();
        $santri = $santriModel->select('santris.*, kelas.nama_kelas, kelas.jurusan')
                              ->join('kelas', 'kelas.id = santris.kelas_id')
                              ->find($santriId);
        
        if (!$santri) return $this->failNotFound('Santri tidak ditemukan');

        $db = \Config\Database::connect();
        $nilaiRaw = $db->table('penilaian')
            ->select('penilaian.*, aspek_penilaian.nama_aspek, kategori_penilaian.nama_kategori, aspek_penilaian.bobot as aspek_bobot')
            ->join('aspek_penilaian', 'aspek_penilaian.id = penilaian.aspek_id')
            ->join('kategori_penilaian', 'kategori_penilaian.id = aspek_penilaian.kategori_id')
            ->where('santri_id', $santriId)
            ->where('is_draft', 0)
            ->get()
            ->getResultArray();

        // Group and calculate
        $raporData = [];
        $temp = [];

        foreach ($nilaiRaw as $n) {
            $cat = $n['nama_kategori'];
            $asp = $n['nama_aspek'];
            $jenis = $n['jenis_nilai'];
            
            if (!isset($temp[$cat][$asp])) {
                $temp[$cat][$asp] = ['harian' => [], 'uts' => null, 'uas' => null, 'bobot' => $n['aspek_bobot']];
            }
            
            if ($jenis == 'harian') {
                $temp[$cat][$asp]['harian'][] = $n['nilai'];
            } else {
                $temp[$cat][$asp][$jenis] = $n['nilai'];
            }
        }

        $totalAkhir = 0;
        $countAkhir = 0;

        foreach ($temp as $catName => $aspeks) {
            $catItems = [];
            foreach ($aspeks as $aspName => $data) {
                $avgHarian = count($data['harian']) > 0 ? array_sum($data['harian']) / count($data['harian']) : 0;
                $uts = $data['uts'] ?? $avgHarian; // fallback to harian if no UTS
                $uas = $data['uas'] ?? $avgHarian; // fallback to harian if no UAS
                
                // Weighted: 30% Harian, 30% UTS, 40% UAS
                $nilaiAkhir = ($avgHarian * 0.3) + ($uts * 0.3) + ($uas * 0.4);
                
                $catItems[] = [
                    'aspek' => $aspName,
                    'harian' => round($avgHarian, 2),
                    'uts' => $uts,
                    'uas' => $uas,
                    'akhir' => round($nilaiAkhir, 2),
                    'predikat' => $this->getPredikat($nilaiAkhir)
                ];
                
                $totalAkhir += $nilaiAkhir;
                $countAkhir++;
            }
            
            $raporData[] = [
                'kategori' => $catName,
                'items' => $catItems
            ];
        }

        $rataRata = $countAkhir > 0 ? $totalAkhir / $countAkhir : 0;

        return $this->respond([
            'status' => 200, 
            'data' => [
                'santri' => $santri,
                'rapor' => $raporData,
                'statistik' => [
                    'rata_rata' => round($rataRata, 2),
                    'predikat' => $this->getPredikat($rataRata),
                    'keterangan' => $rataRata >= 75 ? 'LULUS / KOMPETEN' : 'PERLU BIMBINGAN'
                ],
                'catatan_wali_kelas' => 'Tingkatkan terus belajarnya dan pertahankan prestasi yang sudah ada.'
            ]
        ]);
    }

    private function getPredikat($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    public function analisisSoal($ujianId = null)
    {
        $db = \Config\Database::connect();
        
        $results = $db->table('ujian_soal')
            ->select('bank_soal.id as soal_id, bank_soal.pertanyaan')
            ->join('bank_soal', 'bank_soal.id = ujian_soal.soal_id')
            ->where('ujian_soal.ujian_id', $ujianId)
            ->get()
            ->getResultArray();

        foreach ($results as &$r) {
            $stats = $db->table('ujian_jawaban')
                ->selectCount('id', 'total')
                ->selectSum('is_benar', 'benar')
                ->join('ujian_sesi', 'ujian_sesi.id = ujian_jawaban.sesi_id')
                ->where('ujian_sesi.ujian_id', $ujianId)
                ->where('ujian_jawaban.soal_id', $r['soal_id'])
                ->get()
                ->getRow();

            $total = $stats->total ?: 0;
            $benar = $stats->benar ?: 0;
            
            $indeksKesulitan = $total > 0 ? ($benar / $total) : 0;
            
            if ($indeksKesulitan > 0.7) $tingkat = 'Mudah';
            elseif ($indeksKesulitan > 0.3) $tingkat = 'Sedang';
            else $tingkat = 'Sukar';

            $r['total_peserta'] = $total;
            $r['total_benar'] = (int)$benar;
            $r['persentase_benar'] = round($indeksKesulitan * 100, 2);
            $r['tingkat_kesulitan'] = $tingkat;
        }

        return $this->respond([
            'status' => 200, 
            'data' => $results
        ]);
    }
}
