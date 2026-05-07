<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UjianModel;
use App\Models\UjianSoalModel;

class UjianController extends ResourceController
{
    protected $modelName = 'App\Models\UjianModel';
    protected $format    = 'json';

    public function index()
    {
        $model = new UjianModel();
        $data = $model->getWithRelations();
        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function create()
    {
        $model = new UjianModel();
        $data = $this->request->getJSON(true);

        if (isset($data['kelas_ids']) && is_array($data['kelas_ids'])) {
            $data['kelas_ids'] = json_encode($data['kelas_ids']);
        }

        if ($model->insert($data)) {
            $ujianId = $model->getInsertID();
            
            // Insert Soal
            if (isset($data['soal_ids']) && is_array($data['soal_ids'])) {
                $ujianSoalModel = new UjianSoalModel();
                $urutan = 1;
                foreach ($data['soal_ids'] as $soalId) {
                    $ujianSoalModel->insert([
                        'ujian_id' => $ujianId,
                        'soal_id' => $soalId,
                        'urutan' => $urutan++,
                        'bobot_nilai' => 1.0 // Default
                    ]);
                }
            }
            
            $data['id'] = $ujianId;
            return $this->respondCreated(['status' => 201, 'message' => 'Ujian berhasil dibuat', 'data' => $data]);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function show($id = null)
    {
        $model = new UjianModel();
        $data = $model->getWithRelations($id);
        if ($data) {
            $ujianSoalModel = new UjianSoalModel();
            $soalIds = $ujianSoalModel->where('ujian_id', $id)->findColumn('soal_id') ?? [];
            $data['soal_ids'] = $soalIds;
            return $this->respond(['status' => 200, 'data' => $data]);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }

    public function update($id = null)
    {
        $model = new UjianModel();
        $data = $this->request->getJSON(true);

        if (isset($data['kelas_ids']) && is_array($data['kelas_ids'])) {
            $data['kelas_ids'] = json_encode($data['kelas_ids']);
        }

        if ($model->update($id, $data)) {
            return $this->respond(['status' => 200, 'message' => 'Ujian berhasil diupdate']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function delete($id = null)
    {
        $model = new UjianModel();
        if ($model->delete($id)) {
            $ujianSoalModel = new UjianSoalModel();
            $ujianSoalModel->where('ujian_id', $id)->delete();
            return $this->respondDeleted(['status' => 200, 'message' => 'Ujian berhasil dihapus']);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }

    public function generateToken($id = null)
    {
        $model = new UjianModel();
        $ujian = $model->find($id);
        if (!$ujian) return $this->failNotFound('Ujian tidak ditemukan');

        $token = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $model->update($id, ['token_akses' => $token]);

        return $this->respond(['status' => 200, 'message' => 'Token berhasil di-generate', 'data' => ['token_akses' => $token]]);
    }

    public function monitor($id = null)
    {
        $sesiModel = new \App\Models\UjianSesiModel();
        $data = $sesiModel->select('ujian_sesi.*, santris.nama_lengkap as nama_santri, santris.nis')
                          ->join('santris', 'santris.id = ujian_sesi.santri_id')
                          ->where('ujian_id', $id)
                          ->findAll();
                          
        return $this->respond(['status' => 200, 'data' => $data]);
    }
}
