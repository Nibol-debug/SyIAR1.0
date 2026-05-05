<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use CodeIgniter\RESTful\ResourceController;

class KelasController extends ResourceController
{
    protected $modelName = 'App\Models\KelasModel';
    protected $format = 'json';
    
    public function index()
    {
        $jurusan = $this->request->getGet('jurusan');
        $model = new KelasModel();
        
        if ($jurusan) {
            $data = $model->getActiveByJurusan($jurusan);
        } else {
            $data = $model->getWithWali();
        }
        
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function create()
    {
        $rules = [
            'nama_kelas' => 'required',
            'jurusan' => 'required',
            'tahun_ajaran' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $id = $this->model->insert($this->request->getJSON(true));
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => ['id' => $id]
        ]);
    }
    
    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Kelas tidak ditemukan');
        
        $this->model->update($id, $this->request->getJSON(true));
        
        return $this->respond(['success' => true, 'message' => 'Kelas berhasil diupdate']);
    }
    
    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Kelas tidak ditemukan');
        
        $this->model->delete($id);
        
        return $this->respondDeleted(['success' => true, 'message' => 'Kelas berhasil dihapus']);
    }
}
