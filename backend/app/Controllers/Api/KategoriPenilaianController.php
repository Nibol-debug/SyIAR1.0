<?php

namespace App\Controllers\Api;

use App\Models\KategoriPenilaianModel;
use CodeIgniter\RESTful\ResourceController;

class KategoriPenilaianController extends ResourceController
{
    protected $modelName = 'App\Models\KategoriPenilaianModel';
    protected $format = 'json';
    
    public function index()
    {
        $model = new KategoriPenilaianModel();
        $activeOnly = $this->request->getGet('active_only');
        
        $data = $activeOnly ? $model->getActiveOrdered() : $model->orderBy('urutan')->findAll();
        
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Kategori tidak ditemukan');
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function create()
    {
        $rules = [
            'nama_kategori' => 'required|min_length[3]',
            'kode_kategori' => 'required|is_unique[kategori_penilaian.kode_kategori]',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $id = $this->model->insert($this->request->getJSON(true));
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Kategori penilaian berhasil ditambahkan',
            'data'    => ['id' => $id]
        ]);
    }
    
    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Kategori tidak ditemukan');
        
        $this->model->update($id, $this->request->getJSON(true));
        
        return $this->respond(['success' => true, 'message' => 'Kategori berhasil diupdate']);
    }
    
    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Kategori tidak ditemukan');
        
        $this->model->delete($id);
        
        return $this->respondDeleted(['success' => true, 'message' => 'Kategori berhasil dihapus']);
    }
}
