<?php

namespace App\Controllers\Api;

use App\Models\PresensiPegawaiModel;
use CodeIgniter\RESTful\ResourceController;

class PresensiPegawaiController extends ResourceController
{
    protected $modelName = 'App\Models\PresensiPegawaiModel';
    protected $format = 'json';

    public function index()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $model = new PresensiPegawaiModel();
        $data = $model->getByTanggal($tanggal);
        return $this->respond(['success' => true, 'data' => $data, 'tanggal' => $tanggal]);
    }

    public function rekap()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $model = new PresensiPegawaiModel();
        $data = $model->getRekap($bulan);
        return $this->respond(['success' => true, 'data' => $data, 'bulan' => $bulan]);
    }

    public function batch()
    {
        $json = $this->request->getJSON(true);
        if (empty($json['tanggal']) || empty($json['records'])) {
            return $this->fail('Data presensi tidak lengkap');
        }

        $tanggal = $json['tanggal'];
        $records = $json['records'];
        $batchData = [];

        foreach ($records as $r) {
            $batchData[] = [
                'pegawai_id' => $r['pegawai_id'],
                'tanggal'    => $tanggal,
                'status'     => $r['status'],
                'jam_masuk'  => $r['jam_masuk'] ?? null,
                'jam_keluar' => $r['jam_keluar'] ?? null,
                'keterangan' => $r['keterangan'] ?? null,
            ];
        }

        $model = new PresensiPegawaiModel();
        $model->saveBatchPresensi($batchData);

        return $this->respondCreated(['success' => true, 'message' => 'Presensi pegawai berhasil disimpan']);
    }
}
