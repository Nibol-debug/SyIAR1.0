<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TahunAjaranModel;

class TahunAjaranController extends ResourceController
{
    protected $modelName = 'App\Models\TahunAjaranModel';
    protected $format    = 'json';

    public function index()
    {
        $data = $this->model->findAll();
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
            return $this->respondCreated(['message' => 'Tahun Ajaran berhasil ditambahkan']);
        }
        return $this->fail('Gagal menambahkan data');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        $rules = $this->model->validationRules;
        // make rules optional for update
        foreach($rules as $key => $rule) {
            $rules[$key] = str_replace('required', 'permit_empty', $rule);
        }

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Tahun Ajaran berhasil diupdate']);
        }
        return $this->fail('Gagal mengupdate data');
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Tahun Ajaran berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}
