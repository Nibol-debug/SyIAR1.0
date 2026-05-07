<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'nama_kelas', 'jurusan', 'tahun_ajaran',
        'wali_kelas_id', 'kapasitas', 'keterangan', 'is_active'
    ];
    protected $useTimestamps = true;
    
    // Daftar jurusan yang valid
    const JURUSAN_LIST = [
        'TKJ' => 'Teknik Komputer & Jaringan',
        'TB' => 'Tata Busana',
        'DG' => 'Desain Grafis',
        'FV' => 'Fotografi & Videografi',
        'TSM' => 'Teknik Sepeda Motor',
        'DOS' => 'Digital Office Specialist'
    ];
    
    public function getActiveByJurusan($jurusan)
    {
        return $this->where('jurusan', $jurusan)
                   ->where('is_active', true)
                   ->orderBy('nama_kelas')
                   ->findAll();
    }
    
    public function getWithWali()
    {
        return $this->select('kelas.*, users.nama_lengkap as wali_nama')
                   ->join('users', 'users.id = kelas.wali_kelas_id', 'left')
                   ->where('kelas.is_active', true)
                   ->orderBy('kelas.jurusan, kelas.nama_kelas')
                   ->findAll();
    }
    
    /**
     * Get all kelas grouped by jurusan
     */
    public function getAllGroupedByJurusan()
    {
        $kelas = $this->where('is_active', true)
                     ->orderBy('jurusan, nama_kelas')
                     ->findAll();
        
        $grouped = [];
        foreach (self::JURUSAN_LIST as $code => $nama) {
            $grouped[$code] = [
                'nama_jurusan' => $nama,
                'kelas' => []
            ];
        }
        
        foreach ($kelas as $k) {
            if (isset($grouped[$k['jurusan']])) {
                $grouped[$k['jurusan']]['kelas'][] = $k;
            }
        }
        
        return $grouped;
    }
    
    /**
     * Validate jurusan code
     */
    public static function isValidJurusan($code)
    {
        return isset(self::JURUSAN_LIST[$code]);
    }
    
    /**
     * Get jurusan name by code
     */
    public static function getJurusanName($code)
    {
        return self::JURUSAN_LIST[$code] ?? null;
    }
    
    /**
     * Get all valid jurusan codes
     */
    public static function getJurusanCodes()
    {
        return array_keys(self::JURUSAN_LIST);
    }
}
