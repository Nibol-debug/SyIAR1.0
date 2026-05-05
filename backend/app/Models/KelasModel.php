<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'nama_kelas', 'jurusan', 'tahun_ajaran',
        'wali_kelas_id', 'kapasitas', 'keterangan', 'is_active'
    ];
    protected $useTimestamps = true;
    
    public function getActiveByJurusan($jurusan)
    {
        return $this->where('jurusan', $jurusan)
                   ->where('is_active', true)
                   ->orderBy('nama_kelas')
                   ->findAll();
    }
    
    public function getWithWali()
    {
        return $this->select('kelas.*, users.nama_lengkap as wali_nama')
                   ->join('users', 'users.id = kelas.wali_kelas_id', 'left')
                   ->where('kelas.is_active', true)
                   ->findAll();
    }
}
