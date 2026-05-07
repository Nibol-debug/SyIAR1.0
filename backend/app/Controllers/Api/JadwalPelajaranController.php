<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\JadwalPelajaranModel;

class JadwalPelajaranController extends ResourceController
{
    protected $modelName = 'App\Models\JadwalPelajaranModel';
    protected $format    = 'json';

    public function index()
    {
        $this->model->select('jadwal_pelajaran.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, pegawai.nama_lengkap as nama_guru, tahun_ajaran.nama as nama_tahun_ajaran')
                    ->join('kelas', 'kelas.id = jadwal_pelajaran.kelas_id', 'left')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_pelajaran.mapel_id', 'left')
                    ->join('pegawai', 'pegawai.id = jadwal_pelajaran.guru_id', 'left')
                    ->join('tahun_ajaran', 'tahun_ajaran.id = jadwal_pelajaran.tahun_ajaran_id', 'left');
                    
        $data = $this->model->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $this->model->select('jadwal_pelajaran.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, pegawai.nama_lengkap as nama_guru, tahun_ajaran.nama as nama_tahun_ajaran')
                    ->join('kelas', 'kelas.id = jadwal_pelajaran.kelas_id', 'left')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_pelajaran.mapel_id', 'left')
                    ->join('pegawai', 'pegawai.id = jadwal_pelajaran.guru_id', 'left')
                    ->join('tahun_ajaran', 'tahun_ajaran.id = jadwal_pelajaran.tahun_ajaran_id', 'left');
                    
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
        
        // Simple clash detection (guru bentrok)
        $clash = $this->model->where('guru_id', $data['guru_id'])
                             ->where('hari', $data['hari'])
                             ->where('jam_mulai <', $data['jam_selesai'])
                             ->where('jam_selesai >', $data['jam_mulai'])
                             ->first();
                             
        if ($clash) {
            return $this->fail('Jadwal bentrok dengan jadwal guru yang sama pada hari dan jam tersebut.');
        }
        
        if ($this->model->insert($data)) {
            return $this->respondCreated(['message' => 'Jadwal berhasil ditambahkan']);
        }
        return $this->fail('Gagal menambahkan data');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Jadwal berhasil diupdate']);
        }
        return $this->fail('Gagal mengupdate data');
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Jadwal berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}
