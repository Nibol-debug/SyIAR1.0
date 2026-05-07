<?php

namespace App\Models;

use CodeIgniter\Model;

class PresensiPegawaiModel extends Model
{
    protected $table = 'presensi_pegawai';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['pegawai_id', 'tanggal', 'jam_masuk', 'jam_keluar', 'status', 'keterangan'];
    protected $useTimestamps = true;

    public function getRekap($bulan)
    {
        return $this->select('pegawai.nama_lengkap, presensi_pegawai.*')
                    ->join('pegawai', 'pegawai.id = presensi_pegawai.pegawai_id')
                    ->like('presensi_pegawai.tanggal', $bulan, 'after')
                    ->orderBy('presensi_pegawai.tanggal', 'DESC')
                    ->orderBy('pegawai.nama_lengkap', 'ASC')
                    ->findAll();
    }

    public function getByTanggal($tanggal)
    {
        // Ambil semua pegawai aktif dan left join dengan presensi pada tanggal tersebut
        $db = \Config\Database::connect();
        return $db->table('pegawai')
            ->select('pegawai.id as pegawai_id, pegawai.nama_lengkap, pegawai.jabatan, p.id as presensi_id, p.status, p.jam_masuk, p.jam_keluar, p.keterangan')
            ->join('presensi_pegawai p', "p.pegawai_id = pegawai.id AND p.tanggal = '{$tanggal}'", 'left')
            ->where('pegawai.is_active', 1)
            ->orderBy('pegawai.nama_lengkap', 'ASC')
            ->get()->getResultArray();
    }

    public function saveBatchPresensi($data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        $inserted = 0;
        foreach ($data as $row) {
            $existing = $builder->where('pegawai_id', $row['pegawai_id'])
                                ->where('tanggal', $row['tanggal'])
                                ->get()->getRow();
            
            if ($existing) {
                $builder->where('id', $existing->id)->update($row);
            } else {
                $builder->insert($row);
            }
            $inserted++;
        }
        return $inserted;
    }
}
