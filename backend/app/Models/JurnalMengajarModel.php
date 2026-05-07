<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalMengajarModel extends Model
{
    protected $table            = 'jurnal_mengajar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['guru_id', 'kelas_id', 'mapel_id', 'tanggal', 'jam_ke', 'materi', 'tugas', 'keterangan'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'guru_id'         => 'required|numeric',
        'kelas_id'        => 'required|numeric',
        'mapel_id'        => 'required|numeric',
        'tanggal'         => 'required|valid_date',
        'jam_ke'          => 'permit_empty|max_length[10]',
        'materi'          => 'required',
        'tugas'           => 'permit_empty',
        'keterangan'      => 'permit_empty'
    ];
}
