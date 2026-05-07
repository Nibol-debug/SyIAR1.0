<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UjianSesiModel;
use App\Models\UjianModel;
use App\Models\UjianSoalModel;
use App\Models\UjianJawabanModel;
use App\Models\UjianPelanggaranModel;

class UjianSesiController extends ResourceController
{
    protected $format = 'json';

    public function mulai()
    {
        $data = $this->request->getJSON(true);
        $token = $data['token_akses'] ?? null;
        $santriId = $data['santri_id'] ?? null;

        if (!$token || !$santriId) return $this->failValidationErrors('Token dan Santri ID wajib diisi');

        $ujianModel = new UjianModel();
        $ujian = $ujianModel->where('token_akses', $token)->first();

        if (!$ujian) return $this->failNotFound('Token ujian tidak valid');
        if ($ujian['status'] !== 'aktif') return $this->failValidationErrors('Ujian belum aktif');

        $sesiModel = new UjianSesiModel();
        $sesi = $sesiModel->where('ujian_id', $ujian['id'])
                          ->where('santri_id', $santriId)
                          ->first();

        if (!$sesi) {
            $sesiModel->insert([
                'ujian_id' => $ujian['id'],
                'santri_id' => $santriId,
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'status' => 'mengerjakan',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ]);
            $sesiId = $sesiModel->getInsertID();
        } else {
            if ($sesi['status'] === 'selesai' || $sesi['status'] === 'diskualifikasi') {
                return $this->failValidationErrors('Anda sudah menyelesaikan ujian ini');
            }
            $sesiId = $sesi['id'];
        }

        return $this->respond(['status' => 200, 'message' => 'Ujian dimulai', 'data' => ['sesi_id' => $sesiId, 'ujian_id' => $ujian['id']]]);
    }

    public function getSoal($sesiId = null)
    {
        $sesiModel = new UjianSesiModel();
        $sesi = $sesiModel->find($sesiId);
        if (!$sesi) return $this->failNotFound('Sesi ujian tidak ditemukan');

        $ujianModel = new UjianModel();
        $ujian = $ujianModel->find($sesi['ujian_id']);

        $db = \Config\Database::connect();
        $soalBuilder = $db->table('ujian_soal')
                          ->select('bank_soal.id, bank_soal.pertanyaan, bank_soal.tipe_soal, bank_soal.pilihan_jawaban')
                          ->join('bank_soal', 'bank_soal.id = ujian_soal.soal_id')
                          ->where('ujian_soal.ujian_id', $sesi['ujian_id']);

        if ($ujian['shuffle_soal']) {
            $soalBuilder->orderBy('RAND()');
        } else {
            $soalBuilder->orderBy('ujian_soal.urutan', 'ASC');
        }

        $soal = $soalBuilder->get()->getResultArray();
        
        // Parse JSON for pilihan_jawaban
        foreach ($soal as &$s) {
            if ($s['pilihan_jawaban']) {
                $pilihan = json_decode($s['pilihan_jawaban'], true);
                if ($ujian['shuffle_jawaban'] && is_array($pilihan)) {
                    shuffle($pilihan);
                }
                $s['pilihan_jawaban'] = $pilihan;
            }
        }

        return $this->respond(['status' => 200, 'data' => $soal]);
    }

    public function simpanJawaban($sesiId = null)
    {
        $data = $this->request->getJSON(true);
        $jawabanModel = new UjianJawabanModel();
        
        $existing = $jawabanModel->where('sesi_id', $sesiId)
                                 ->where('soal_id', $data['soal_id'])
                                 ->first();

        // Check if correct
        $bankSoalModel = new \App\Models\BankSoalModel();
        $soal = $bankSoalModel->find($data['soal_id']);
        
        $isBenar = null;
        if ($soal['tipe_soal'] == 'pg') {
            $isBenar = ($soal['kunci_jawaban'] == $data['jawaban_siswa']) ? 1 : 0;
        }

        $saveData = [
            'sesi_id' => $sesiId,
            'soal_id' => $data['soal_id'],
            'jawaban_siswa' => $data['jawaban_siswa'],
            'is_benar' => $isBenar
        ];

        if ($existing) {
            $jawabanModel->update($existing['id'], $saveData);
        } else {
            $jawabanModel->insert($saveData);
        }

        return $this->respond(['status' => 200, 'message' => 'Jawaban disimpan']);
    }

    public function submit($sesiId = null)
    {
        $sesiModel = new UjianSesiModel();
        $sesiModel->update($sesiId, [
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'status' => 'selesai'
        ]);

        return $this->respond(['status' => 200, 'message' => 'Ujian berhasil disubmit']);
    }

    public function catatPelanggaran($sesiId = null)
    {
        $data = $this->request->getJSON(true);
        $pelanggaranModel = new UjianPelanggaranModel();
        $pelanggaranModel->insert([
            'sesi_id' => $sesiId,
            'jenis' => $data['jenis'],
            'waktu' => date('Y-m-d H:i:s'),
            'detail' => $data['detail'] ?? null
        ]);

        $sesiModel = new UjianSesiModel();
        $sesi = $sesiModel->find($sesiId);
        $count = $sesi['peringatan_count'] + 1;
        $sesiModel->update($sesiId, ['peringatan_count' => $count]);

        return $this->respond(['status' => 200, 'message' => 'Pelanggaran dicatat']);
    }
}
