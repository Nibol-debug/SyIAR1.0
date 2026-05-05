<?php

namespace App\Controllers\Api;

use App\Models\AspekPenilaianModel;
use CodeIgniter\RESTful\ResourceController;

class AspekPenilaianController extends ResourceController
{
    protected $modelName = 'App\Models\AspekPenilaianModel';
    protected $format = 'json';
    
    public function index()
    {
        $model = new AspekPenilaianModel();
        $kategoriId = $this->request->getGet('kategori_id');
        
        $data = $kategoriId ? $model->getByKategori($kategoriId) : $model->getWithKategori();
        
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Aspek tidak ditemukan');
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function create()
    {
        $rules = [
            'kategori_id' => 'required|integer',
            'nama_aspek'  => 'required|min_length[3]',
            'kode_aspek'  => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $id = $this->model->insert($this->request->getJSON(true));
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Aspek penilaian berhasil ditambahkan',
            'data'    => ['id' => $id]
        ]);
    }
    
    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Aspek tidak ditemukan');
        
        $this->model->update($id, $this->request->getJSON(true));
        
        return $this->respond(['success' => true, 'message' => 'Aspek berhasil diupdate']);
    }
    
    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Aspek tidak ditemukan');
        
        $this->model->delete($id);
        
        return $this->respondDeleted(['success' => true, 'message' => 'Aspek berhasil dihapus']);
    }
}
