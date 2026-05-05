<?php

namespace App\Models;

use CodeIgniter\Model;

class PresensiSiswaModel extends Model
{
    protected $table = 'presensi_siswa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['santri_id', 'kelas_id', 'mapel_id', 'tanggal', 'status', 'keterangan', 'pencatat_id'];
    protected $useTimestamps = true;

    public function getRekapByKelas($kelasId, $bulan)
    {
        return $this->select('santris.nama_lengkap, santris.nis,
                    SUM(CASE WHEN presensi_siswa.status = "H" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN presensi_siswa.status = "I" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN presensi_siswa.status = "S" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN presensi_siswa.status = "A" THEN 1 ELSE 0 END) as alpha')
                    ->join('santris', 'santris.id = presensi_siswa.santri_id')
                    ->where('presensi_siswa.kelas_id', $kelasId)
                    ->where('DATE_FORMAT(presensi_siswa.tanggal, "%Y-%m")', $bulan)
                    ->groupBy('santris.id')
                    ->orderBy('santris.nama_lengkap')
                    ->findAll();
    }

    public function getByTanggal($kelasId, $tanggal)
    {
        return $this->select('presensi_siswa.*, santris.nama_lengkap, santris.nis')
                    ->join('santris', 'santris.id = presensi_siswa.santri_id')
                    ->where('presensi_siswa.kelas_id', $kelasId)
                    ->where('presensi_siswa.tanggal', $tanggal)
                    ->orderBy('santris.nama_lengkap')
                    ->findAll();
    }
}
