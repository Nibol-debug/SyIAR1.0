<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianPelanggaranModel extends Model
{
    protected $table            = 'ujian_pelanggaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['sesi_id', 'jenis', 'waktu', 'detail'];

    protected bool $allowEmptyInserts = false;

    // No Timestamps
    protected $useTimestamps = false;
}
