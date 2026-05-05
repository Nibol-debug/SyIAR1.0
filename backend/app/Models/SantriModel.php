<?php

namespace App\Models;

use CodeIgniter\Model;

class SantriModel extends Model
{
    protected $table = 'santris';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'nis', 'nisn', 'nama_lengkap', 'nama_panggilan', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telepon', 'email',
        'nama_ayah', 'nama_ibu', 'no_hp_ortu', 'pekerjaan_ortu',
        'kelas_id', 'status', 'tanggal_masuk', 'tanggal_keluar', 'foto'
    ];
    protected $useTimestamps = true;
    protected $beforeInsert = ['generateNIS'];
    
    protected function generateNIS(array $data)
    {
        if (empty($data['data']['nis'])) {
            $tahun = date('Y');
            $last = $this->orderBy('id', 'DESC')->first();
            $no = $last ? str_pad((intval(substr($last['nis'], -4)) + 1), 4, '0', STR_PAD_LEFT) : '0001';
            $data['data']['nis'] = 'SANTRI/' . $tahun . '/' . $no;
        }
        return $data;
    }
    
    public function search($keyword = null)
    {
        if ($keyword) {
            return $this->like('nama_lengkap', $keyword)
                       ->orLike('nis', $keyword)
                       ->orLike('nisn', $keyword);
        }
        return $this;
    }
    
    public function getByKelas($kelasId)
    {
        return $this->where('kelas_id', $kelasId)->where('status', 'aktif')->findAll();
    }
}
