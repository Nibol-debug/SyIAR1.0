<?php

namespace App\Controllers\Api;

use App\Models\MataPelajaranModel;
use CodeIgniter\RESTful\ResourceController;

class MataPelajaranController extends ResourceController
{
    protected $modelName = 'App\Models\MataPelajaranModel';
    protected $format = 'json';

    public function index()
    {
        $model = new MataPelajaranModel();
        $kelompok = $this->request->getGet('kelompok');
        $data = $model->getActiveByKelompok($kelompok);
        return $this->respond(['success' => true, 'data' => $data]);
    }

    public function create()
    {
        $rules = [
            'kode_mapel' => 'required|is_unique[mata_pelajaran.kode_mapel]',
            'nama_mapel' => 'required|min_length[3]',
        ];
        if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());
        $id = $this->model->insert($this->request->getJSON(true));
        return $this->respondCreated(['success' => true, 'message' => 'Mata pelajaran berhasil ditambahkan', 'data' => ['id' => $id]]);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Mata pelajaran tidak ditemukan');
        $this->model->update($id, $this->request->getJSON(true));
        return $this->respond(['success' => true, 'message' => 'Mata pelajaran berhasil diupdate']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Mata pelajaran tidak ditemukan');
        $this->model->delete($id);
        return $this->respondDeleted(['success' => true, 'message' => 'Mata pelajaran berhasil dihapus']);
    }
}
