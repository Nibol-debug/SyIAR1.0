<?php

namespace App\Controllers\Api;

use App\Models\PegawaiModel;
use CodeIgniter\RESTful\ResourceController;

class PegawaiController extends ResourceController
{
    protected $modelName = 'App\Models\PegawaiModel';
    protected $format = 'json';

    public function index()
    {
        $model = new PegawaiModel();
        $keyword = $this->request->getGet('search');
        $status  = $this->request->getGet('status_kepegawaian');
        
        $query = $keyword ? $model->search($keyword) : $model;
        if ($status) $query = $query->where('status_kepegawaian', $status);
        
        $data = $query->orderBy('nama_lengkap')->paginate(20);
        return $this->respond(['success' => true, 'data' => $data, 'pager' => $model->pager]);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Pegawai tidak ditemukan');
        
        // Get mapel assignments
        $mapel = $this->model->db->table('pegawai_mapel')
            ->select('mata_pelajaran.*')
            ->join('mata_pelajaran', 'mata_pelajaran.id = pegawai_mapel.mapel_id')
            ->where('pegawai_mapel.pegawai_id', $id)
            ->get()->getResultArray();
        $data['mapel'] = $mapel;
        
        return $this->respond(['success' => true, 'data' => $data]);
    }

    public function create()
    {
        $rules = [
            'nama_lengkap'  => 'required|min_length[3]',
            'jenis_kelamin' => 'required|in_list[L,P]',
        ];
        if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());
        
        $id = $this->model->insert($this->request->getJSON(true));
        return $this->respondCreated(['success' => true, 'message' => 'Pegawai berhasil ditambahkan', 'data' => ['id' => $id]]);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Pegawai tidak ditemukan');
        $this->model->update($id, $this->request->getJSON(true));
        return $this->respond(['success' => true, 'message' => 'Pegawai berhasil diupdate']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Pegawai tidak ditemukan');
        $this->model->delete($id);
        return $this->respondDeleted(['success' => true, 'message' => 'Pegawai berhasil dihapus']);
    }

    public function statistik()
    {
        $db = \Config\Database::connect();
        $total = $db->table('pegawai')->where('is_active', 1)->countAllResults();
        $byStatus = $db->table('pegawai')
            ->select('status_kepegawaian, COUNT(*) as total')
            ->where('is_active', 1)
            ->groupBy('status_kepegawaian')
            ->get()->getResultArray();
        
        return $this->respond(['success' => true, 'data' => ['total' => $total, 'by_status' => $byStatus]]);
    }
}
