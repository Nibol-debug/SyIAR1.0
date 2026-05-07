<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianSoalModel extends Model
{
    protected $table            = 'ujian_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ujian_id', 'soal_id', 'urutan', 'bobot_nilai'];

    protected bool $allowEmptyInserts = false;

    // No Timestamps
    protected $useTimestamps = false;
}
