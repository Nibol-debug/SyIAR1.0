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
        
        if (empty($json['santri_id']) || empty($json['values'])) {
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
        
        foreach ($json['values'] as $aspekId => $nilai) {
            $batchData[] = [
                'santri_id'          => $json['santri_id'],
                'aspek_id'           => $aspekId,
                'guru_id'            => $guruId,
                'nilai'              => $nilai,
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
}
