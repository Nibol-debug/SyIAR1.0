<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table = 'penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'santri_id', 'aspek_id', 'guru_id', 'nilai', 'jenis_nilai', 'keterangan',
        'tanggal_penilaian', 'periode', 'is_draft'
    ];
    protected $useTimestamps = true;
    
    public function getBySantri($santriId, $periode = null)
    {
        $query = $this->where('santri_id', $santriId)->where('is_draft', false);
        if ($periode) $query->where('periode', $periode);
        return $query->findAll();
    }
    
    public function getRekapByKelas($kelasId, $periode)
    {
        return $this->select('santris.nama_lengkap, santris.nis, aspek_penilaian.nama_aspek, penilaian.nilai, kategori_penilaian.nama_kategori')
                   ->join('santris', 'santris.id = penilaian.santri_id')
                   ->join('aspek_penilaian', 'aspek_penilaian.id = penilaian.aspek_id')
                   ->join('kategori_penilaian', 'kategori_penilaian.id = aspek_penilaian.kategori_id')
                   ->where('santris.kelas_id', $kelasId)
                   ->where('penilaian.periode', $periode)
                   ->where('penilaian.is_draft', false)
                   ->orderBy('santris.nama_lengkap, kategori_penilaian.urutan')
                   ->findAll();
    }
    
    public function saveBatchSantri($data)
    {
        return $this->db->table($this->table)->insertBatch($data);
    }
}
