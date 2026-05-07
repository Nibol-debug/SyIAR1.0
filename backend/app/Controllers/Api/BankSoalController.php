<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BankSoalModel;

class BankSoalController extends ResourceController
{
    protected $modelName = 'App\Models\BankSoalModel';
    protected $format    = 'json';

    public function index()
    {
        $model = new BankSoalModel();
        
        // Handle filter by mapel_id
        $mapel_id = $this->request->getVar('mapel_id');
        if ($mapel_id) {
            $model->where('mapel_id', $mapel_id);
        }

        $data = $model->getWithRelations();
        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function create()
    {
        $model = new BankSoalModel();
        $data = $this->request->getJSON(true);

        if ($data && isset($data['pilihan_jawaban']) && is_array($data['pilihan_jawaban'])) {
            $data['pilihan_jawaban'] = json_encode($data['pilihan_jawaban']);
        }

        if ($model->insert($data)) {
            $data['id'] = $model->getInsertID();
            return $this->respondCreated(['status' => 201, 'message' => 'Soal berhasil ditambahkan', 'data' => $data]);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function show($id = null)
    {
        $model = new BankSoalModel();
        $data = $model->getWithRelations($id);
        if ($data) {
            return $this->respond(['status' => 200, 'data' => $data]);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }

    public function update($id = null)
    {
        $model = new BankSoalModel();
        $data = $this->request->getJSON(true);
        
        if ($data && isset($data['pilihan_jawaban']) && is_array($data['pilihan_jawaban'])) {
            $data['pilihan_jawaban'] = json_encode($data['pilihan_jawaban']);
        }

        if ($model->update($id, $data)) {
            return $this->respond(['status' => 200, 'message' => 'Soal berhasil diupdate']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function delete($id = null)
    {
        $model = new BankSoalModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'Soal berhasil dihapus']);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }
}
