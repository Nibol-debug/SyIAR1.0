<?php

namespace App\Models;

use CodeIgniter\Model;

class BankSoalModel extends Model
{
    protected $table            = 'bank_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mapel_id', 'guru_id', 'pertanyaan', 'tipe_soal', 
        'pilihan_jawaban', 'kunci_jawaban', 'tingkat_kesulitan', 
        'topik', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWithRelations($id = null)
    {
        $builder = $this->select('bank_soal.*, mata_pelajaran.nama_mapel, pegawai.nama_lengkap as nama_guru')
                        ->join('mata_pelajaran', 'mata_pelajaran.id = bank_soal.mapel_id', 'left')
                        ->join('pegawai', 'pegawai.id = bank_soal.guru_id', 'left');
        
        if ($id) {
            return $builder->where('bank_soal.id', $id)->first();
        }
        return $builder->findAll();
    }
}
