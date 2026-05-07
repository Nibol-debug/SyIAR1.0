<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\KalenderAkademikModel;

class KalenderAkademikController extends ResourceController
{
    protected $modelName = 'App\Models\KalenderAkademikModel';
    protected $format    = 'json';

    public function index()
    {
        $data = $this->model->orderBy('tanggal_mulai', 'ASC')->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }

    public function create()
    {
        $rules = $this->model->validationRules;
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if ($this->model->insert($data)) {
            return $this->respondCreated(['message' => 'Kalender Akademik berhasil ditambahkan']);
        }
        return $this->fail('Gagal menambahkan data');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Kalender Akademik berhasil diupdate']);
        }
        return $this->fail('Gagal mengupdate data');
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Kalender Akademik berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}
