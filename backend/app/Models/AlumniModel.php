<?php
namespace App\Models;
use CodeIgniter\Model;
class AlumniModel extends Model
{
    protected $table = 'alumni';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'santri_id', 'tahun_lulus', 'nomor_ijazah', 'nomor_skhun',
        'status_after', 'nama_perguruan', 'jurusan', 'nama_perusahaan', 'jabatan',
        'alamat_terkini', 'kontak_terkini', 'email_terkini', 'instagram', 'facebook',
        'kesan_pesan', 'partisipasi_reuni'
    ];
    
    public function getAlumniWithSantri($filters = [], $limit = 20, $offset = 0)
    {
        $builder = $this->select('alumni.*, santri.nis, santri.nama_lengkap, santri.foto')
            ->join('santri', 'santri.id = alumni.santri_id');
        if (!empty($filters['tahun_lulus'])) $builder->where('alumni.tahun_lulus', $filters['tahun_lulus']);
        if (!empty($filters['search'])) $builder->like('santri.nama_lengkap', $filters['search']);
        return [
            'data' => $builder->findAll($limit, $offset),
            'total' => $builder->countAllResults(false),
        ];
    }
}
