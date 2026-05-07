<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
    protected $table            = 'ujian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul', 'mapel_id', 'kelas_ids', 'guru_id', 
        'durasi_menit', 'jumlah_soal', 'tanggal_mulai', 
        'tanggal_selesai', 'token_akses', 'shuffle_soal', 
        'shuffle_jawaban', 'max_peringatan', 'status'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWithRelations($id = null)
    {
        $builder = $this->select('ujian.*, mata_pelajaran.nama_mapel, pegawai.nama_lengkap as nama_guru')
                        ->join('mata_pelajaran', 'mata_pelajaran.id = ujian.mapel_id', 'left')
                        ->join('pegawai', 'pegawai.id = ujian.guru_id', 'left');
        
        if ($id) {
            return $builder->where('ujian.id', $id)->first();
        }
        return $builder->findAll();
    }
}
