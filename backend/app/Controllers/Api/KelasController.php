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
            // Validate jurusan
            if (!KelasModel::isValidJurusan($jurusan)) {
                return $this->fail('Jurusan tidak valid', 400);
            }
            $data = $model->getActiveByJurusan($jurusan);
        } else {
            $data = $model->getWithWali();
        }
        
        return $this->respond([
            'success' => true,
            'data' => $data,
            'jurusan_list' => KelasModel::JURUSAN_LIST
        ]);
    }
    
    public function create()
    {
        $rules = [
            'nama_kelas' => 'required',
            'jurusan' => 'required|in_list[' . implode(',', KelasModel::getJurusanCodes()) . ']',
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
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kelas tidak ditemukan');
        }
        
        $data = $this->request->getJSON(true);
        
        // Validate jurusan if provided
        if (isset($data['jurusan']) && !KelasModel::isValidJurusan($data['jurusan'])) {
            return $this->fail('Jurusan tidak valid', 400);
        }
        
        $this->model->update($id, $data);
        
        return $this->respond([
            'success' => true,
            'message' => 'Kelas berhasil diupdate'
        ]);
    }
    
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kelas tidak ditemukan');
        }
        
        $this->model->delete($id);
        
        return $this->respondDeleted([
            'success' => true,
            'message' => 'Kelas berhasil dihapus'
        ]);
    }
    
    /**
     * GET /api/kelas/grouped
     * Get all kelas grouped by jurusan
     */
    public function grouped()
    {
        $model = new KelasModel();
        $data = $model->getAllGroupedByJurusan();
        
        return $this->respond([
            'success' => true,
            'data' => $data,
            'jurusan_list' => KelasModel::JURUSAN_LIST
        ]);
    }
    
    /**
     * GET /api/kelas/jurusan-list
     * Get list of all valid jurusan
     */
    public function jurusanList()
    {
        return $this->respond([
            'success' => true,
            'jurusan_list' => KelasModel::JURUSAN_LIST,
            'jurusan_codes' => KelasModel::getJurusanCodes()
        ]);
    }
}
