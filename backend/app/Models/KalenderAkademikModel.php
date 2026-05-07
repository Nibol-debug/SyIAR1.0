<?php

namespace App\Models;

use CodeIgniter\Model;

class KalenderAkademikModel extends Model
{
    protected $table            = 'kalender_akademik';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['judul', 'tanggal_mulai', 'tanggal_selesai', 'jenis', 'deskripsi', 'warna', 'is_active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'judul'           => 'required|max_length[200]',
        'tanggal_mulai'   => 'required|valid_date',
        'tanggal_selesai' => 'permit_empty|valid_date',
        'jenis'           => 'required|in_list[ujian,libur,kegiatan,rapat,lainnya]',
        'deskripsi'       => 'permit_empty',
        'warna'           => 'permit_empty|max_length[7]',
        'is_active'       => 'permit_empty|in_list[0,1]'
    ];
}
