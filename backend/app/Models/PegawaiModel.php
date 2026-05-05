<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'user_id', 'nip', 'nuptk', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telepon', 'email',
        'pendidikan_terakhir', 'gelar', 'universitas', 'tahun_lulus',
        'status_kepegawaian', 'jabatan', 'tanggal_bergabung', 'foto', 'is_active'
    ];
    protected $useTimestamps = true;

    public function search($keyword)
    {
        return $this->like('nama_lengkap', $keyword)
                    ->orLike('nip', $keyword)
                    ->orLike('nuptk', $keyword);
    }

    public function getActive()
    {
        return $this->where('is_active', 1)->orderBy('nama_lengkap')->findAll();
    }

    public function getWithMapel()
    {
        return $this->select('pegawai.*, GROUP_CONCAT(mata_pelajaran.nama_mapel SEPARATOR ", ") as mapel')
                    ->join('pegawai_mapel', 'pegawai_mapel.pegawai_id = pegawai.id', 'left')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = pegawai_mapel.mapel_id', 'left')
                    ->where('pegawai.is_active', 1)
                    ->groupBy('pegawai.id')
                    ->orderBy('pegawai.nama_lengkap')
                    ->findAll();
    }
}
