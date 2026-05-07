<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianSesiModel extends Model
{
    protected $table            = 'ujian_sesi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ujian_id', 'santri_id', 'waktu_mulai', 'waktu_selesai', 
        'status', 'peringatan_count', 'ip_address', 'user_agent'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWithRelations($id = null)
    {
        $builder = $this->select('ujian_sesi.*, santris.nama_lengkap as nama_santri, santris.nis, ujian.judul as judul_ujian')
                        ->join('santris', 'santris.id = ujian_sesi.santri_id', 'left')
                        ->join('ujian', 'ujian.id = ujian_sesi.ujian_id', 'left');
        
        if ($id) {
            return $builder->where('ujian_sesi.id', $id)->first();
        }
        return $builder->findAll();
    }
}
