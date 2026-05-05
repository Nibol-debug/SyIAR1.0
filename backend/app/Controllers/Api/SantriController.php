<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\KelasModel;
use CodeIgniter\RESTful\ResourceController;

class SantriController extends ResourceController
{
    protected $modelName = 'App\Models\SantriModel';
    protected $format = 'json';
    
    public function index()
    {
        $model = new SantriModel();
        $keyword = $this->request->getGet('search');
        $kelas = $this->request->getGet('kelas_id');
        $status = $this->request->getGet('status');
        
        $query = $model;
        if ($keyword) $query = $query->search($keyword);
        if ($kelas) $query = $query->where('kelas_id', $kelas);
        if ($status) $query = $query->where('status', $status);
        
        $data = $query->orderBy('nama_lengkap')->paginate(20);
        
        return $this->respond([
            'success' => true,
            'data' => $data,
            'pager' => $model->pager
        ]);
    }
    
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Santri tidak ditemukan');
        
        return $this->respond(['success' => true, 'data' => $data]);
    }
    
    public function create()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tanggal_lahir' => 'required|valid_date',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $id = $this->model->insert($this->request->getJSON(true));
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Data santri berhasil ditambahkan',
            'data' => ['id' => $id]
        ]);
    }
    
    public function update($id = null)
    {
        $santri = $this->model->find($id);
        if (!$santri) return $this->failNotFound('Santri tidak ditemukan');
        
        $rules = ['nama_lengkap' => 'min_length[3]'];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $this->model->update($id, $this->request->getJSON(true));
        
        return $this->respond(['success' => true, 'message' => 'Data berhasil diupdate']);
    }
    
    public function delete($id = null)
    {
        $santri = $this->model->find($id);
        if (!$santri) return $this->failNotFound('Santri tidak ditemukan');
        
        $this->model->delete($id);
        
        return $this->respondDeleted(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
    
    public function import()
    {
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->fail('File CSV tidak valid');
        }
        
        // Simple CSV import logic
        $csv = array_map('str_getcsv', file($file->getTempName()));
        $header = array_shift($csv);
        $success = 0; $failed = 0;
        
        foreach ($csv as $row) {
            $data = array_combine($header, $row);
            if (!empty($data['nama_lengkap'])) {
                $this->model->insert($data);
                $success++;
            } else {
                $failed++;
            }
        }
        
        return $this->respond([
            'success' => true,
            'message' => "Import selesai: $success berhasil, $failed gagal",
            'stats' => ['success' => $success, 'failed' => $failed]
        ]);
    }
    
    public function export()
    {
        $data = $this->model->orderBy('nama_lengkap')->findAll();
        
        // CSV Export
        $output = fopen('php://output', 'w');
        fputcsv($output, ['NIS', 'Nama', 'JK', 'Kelas', 'Status', 'No HP']);
        foreach ($data as $row) {
            fputcsv($output, [
                $row['nis'], $row['nama_lengkap'], $row['jenis_kelamin'],
                $row['kelas_id'], $row['status'], $row['no_telepon']
            ]);
        }
        fclose($output);
        
        return $this->response->download('export_santri_' . date('Ymd') . '.csv', null);
    }
}
