<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\JurnalMengajarModel;

class JurnalMengajarController extends ResourceController
{
    protected $modelName = 'App\Models\JurnalMengajarModel';
    protected $format    = 'json';

    public function index()
    {
        $this->model->select('jurnal_mengajar.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, pegawai.nama_lengkap as nama_guru')
                    ->join('kelas', 'kelas.id = jurnal_mengajar.kelas_id', 'left')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jurnal_mengajar.mapel_id', 'left')
                    ->join('pegawai', 'pegawai.id = jurnal_mengajar.guru_id', 'left')
                    ->orderBy('tanggal', 'DESC');
                    
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
            return $this->respondCreated(['message' => 'Jurnal berhasil ditambahkan']);
        }
        return $this->fail('Gagal menambahkan data');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Jurnal berhasil diupdate']);
        }
        return $this->fail('Gagal mengupdate data');
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Jurnal berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}
