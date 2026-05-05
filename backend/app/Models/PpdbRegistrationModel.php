<?php

namespace App\Models;

use CodeIgniter\Model;

class PpdbRegistrationModel extends Model
{
    protected $table = 'ppdb_registrations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'kode_pendaftaran', 'nama_calon', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'alamat', 'no_telepon', 'email', 'nama_ayah', 'nama_ibu', 'pekerjaan_ortu',
        'asal_sekolah', 'jenjang_daftar', 'jalur_pendaftaran', 'status_pendaftaran',
        'catatan_admin', 'file_kk', 'file_akta', 'file_foto'
    ];
    protected $useTimestamps = true;
    protected $beforeInsert = ['generateKodePendaftaran'];
    
    protected function generateKodePendaftaran(array $data)
    {
        if (empty($data['data']['kode_pendaftaran'])) {
            $tahun = date('Y');
            $bulan = date('m');
            $last = $this->orderBy('id', 'DESC')->first();
            $no = $last ? str_pad((intval(substr($last['kode_pendaftaran'], -4)) + 1), 4, '0', STR_PAD_LEFT) : '0001';
            $data['data']['kode_pendaftaran'] = 'PPDB/' . $tahun . $bulan . '/' . $no;
        }
        return $data;
    }
    
    public function getByStatus($status)
    {
        return $this->where('status_pendaftaran', $status)->orderBy('created_at', 'DESC')->findAll();
    }
    
    public function getStats()
    {
        return $this->select('status_pendaftaran, COUNT(*) as total')
                   ->groupBy('status_pendaftaran')
                   ->get()
                   ->getResultArray();
    }
}
