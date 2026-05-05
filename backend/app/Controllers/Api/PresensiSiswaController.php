<?php

namespace App\Controllers\Api;

use App\Models\PresensiSiswaModel;
use App\Models\SantriModel;
use CodeIgniter\RESTful\ResourceController;

class PresensiSiswaController extends ResourceController
{
    protected $modelName = 'App\Models\PresensiSiswaModel';
    protected $format = 'json';

    // GET /api/presensi-siswa?kelas_id=X&tanggal=YYYY-MM-DD
    public function index()
    {
        $kelasId = $this->request->getGet('kelas_id');
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        if (!$kelasId) return $this->fail('kelas_id diperlukan');
        
        $model = new PresensiSiswaModel();
        $data = $model->getByTanggal($kelasId, $tanggal);
        
        // If no data for this date, return santri list with default H
        if (empty($data)) {
            $santriModel = new SantriModel();
            $santris = $santriModel->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nama_lengkap')->findAll();
            $data = array_map(function($s) use ($tanggal) {
                return [
                    'santri_id'     => $s['id'],
                    'nama_lengkap'  => $s['nama_lengkap'],
                    'nis'           => $s['nis'],
                    'status'        => 'H',
                    'keterangan'    => null,
                    'tanggal'       => $tanggal,
                    'is_new'        => true,
                ];
            }, $santris);
        }
        
        return $this->respond(['success' => true, 'data' => $data, 'tanggal' => $tanggal]);
    }

    // POST /api/presensi-siswa/batch — batch save attendance for a class
    public function batch()
    {
        $json = $this->request->getJSON(true);
        $kelasId  = $json['kelas_id'] ?? null;
        $tanggal  = $json['tanggal'] ?? date('Y-m-d');
        $records  = $json['records'] ?? [];
        
        if (!$kelasId || empty($records)) return $this->fail('Data tidak lengkap');
        
        $model = new PresensiSiswaModel();
        
        // Get guru_id from JWT
        $pencatatId = 1;
        try {
            helper('jwt_helper');
            $token = str_replace('Bearer ', '', $this->request->getHeaderLine('Authorization'));
            $decoded = validateJWT($token);
            if ($decoded && isset($decoded['data']['id'])) $pencatatId = $decoded['data']['id'];
        } catch (\Exception $e) {}
        
        // Delete existing for this date+kelas, then insert
        $model->where('kelas_id', $kelasId)->where('tanggal', $tanggal)->delete();
        
        $batch = [];
        foreach ($records as $rec) {
            $batch[] = [
                'santri_id'    => $rec['santri_id'],
                'kelas_id'     => $kelasId,
                'mapel_id'     => $json['mapel_id'] ?? null,
                'tanggal'      => $tanggal,
                'status'       => $rec['status'] ?? 'H',
                'keterangan'   => $rec['keterangan'] ?? null,
                'pencatat_id'  => $pencatatId,
            ];
        }
        
        $model->db->table('presensi_siswa')->insertBatch($batch);
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Presensi berhasil disimpan (' . count($batch) . ' santri)',
        ]);
    }

    // GET /api/presensi-siswa/rekap?kelas_id=X&bulan=YYYY-MM
    public function rekap()
    {
        $kelasId = $this->request->getGet('kelas_id');
        $bulan   = $this->request->getGet('bulan') ?? date('Y-m');
        
        if (!$kelasId) return $this->fail('kelas_id diperlukan');
        
        $model = new PresensiSiswaModel();
        $data = $model->getRekapByKelas($kelasId, $bulan);
        
        return $this->respond(['success' => true, 'data' => $data, 'bulan' => $bulan]);
    }
}
