<?php

namespace App\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_mapel', 'nama_mapel', 'kelompok', 'deskripsi', 'is_active'];
    protected $useTimestamps = true;

    public function getActiveByKelompok($kelompok = null)
    {
        $q = $this->where('is_active', 1)->orderBy('nama_mapel');
        if ($kelompok) $q->where('kelompok', $kelompok);
        return $q->findAll();
    }
}
