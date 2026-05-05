<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class DashboardController extends BaseController
{
    use ResponseTrait;
    
    public function stats()
    {
        $db = \Config\Database::connect();
        
        $totalSantri   = $db->table('santris')->where('status', 'aktif')->countAllResults();
        $totalAllSantri = $db->table('santris')->countAllResults();
        $totalGuru     = $db->table('user_roles')->where('role_id', 3)->countAllResults();
        $totalKelas    = $db->table('kelas')->where('is_active', 1)->countAllResults();
        $totalPenilaian = $db->table('penilaian')->where('MONTH(tanggal_penilaian)', date('m'))->countAllResults();
        $totalPpdb     = $db->table('ppdb_registrations')->countAllResults();
        
        // Rata-rata nilai
        $avgNilai = $db->table('penilaian')
            ->selectAvg('nilai')
            ->where('is_draft', 0)
            ->get()
            ->getRow();
        
        // Santri per status
        $santriByStatus = $db->table('santris')
            ->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->getResultArray();
        
        return $this->respond([
            'success' => true,
            'data'    => [
                'total_santri_aktif' => $totalSantri,
                'total_santri'       => $totalAllSantri,
                'total_guru'         => $totalGuru,
                'total_kelas'        => $totalKelas,
                'total_penilaian'    => $totalPenilaian,
                'total_ppdb'         => $totalPpdb,
                'rata_rata_nilai'    => round($avgNilai->nilai ?? 0, 1),
                'santri_by_status'   => $santriByStatus,
            ]
        ]);
    }
}
