<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianJawabanModel extends Model
{
    protected $table            = 'ujian_jawaban';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sesi_id', 'soal_id', 'jawaban_siswa', 'is_benar', 
        'nilai_manual', 'pengoreksi_id'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
