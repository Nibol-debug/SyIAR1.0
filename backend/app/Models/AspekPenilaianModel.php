<?php

namespace App\Models;

use CodeIgniter\Model;

class AspekPenilaianModel extends Model
{
    protected $table = 'aspek_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kategori_id', 'nama_aspek', 'kode_aspek', 'deskripsi',
        'skala_min', 'skala_max', 'bobot', 'tipe_input', 'options', 'urutan', 'is_active'
    ];
    protected $useTimestamps = true;
    
    public function getByKategori($kategoriId, $activeOnly = true)
    {
        $query = $this->where('kategori_id', $kategoriId);
        if ($activeOnly) $query->where('is_active', true);
        return $query->orderBy('urutan')->findAll();
    }
    
    public function getWithKategori()
    {
        return $this->select('aspek_penilaian.*, kategori_penilaian.nama_kategori, kategori_penilaian.kode_kategori, kategori_penilaian.warna')
                   ->join('kategori_penilaian', 'kategori_penilaian.id = aspek_penilaian.kategori_id')
                   ->where('aspek_penilaian.is_active', true)
                   ->orderBy('kategori_penilaian.urutan, aspek_penilaian.urutan')
                   ->findAll();
    }
}
