<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriPenilaianModel extends Model
{
    protected $table = 'kategori_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kategori', 'kode_kategori', 'deskripsi', 'warna', 'urutan', 'is_active'];
    protected $useTimestamps = true;
    
    public function getActiveOrdered()
    {
        return $this->where('is_active', true)->orderBy('urutan')->findAll();
    }
}
