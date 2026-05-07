<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalPelajaranModel extends Model
{
    protected $table            = 'jadwal_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kelas_id', 'mapel_id', 'guru_id', 'hari', 'jam_mulai', 'jam_selesai', 'ruangan', 'tahun_ajaran_id'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'kelas_id'        => 'required|numeric',
        'mapel_id'        => 'required|numeric',
        'guru_id'         => 'required|numeric',
        'hari'            => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
        'jam_mulai'       => 'required',
        'jam_selesai'     => 'required',
        'ruangan'         => 'permit_empty|max_length[30]',
        'tahun_ajaran_id' => 'permit_empty|numeric'
    ];
}
