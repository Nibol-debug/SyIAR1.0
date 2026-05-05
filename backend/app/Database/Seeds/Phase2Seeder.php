<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase2Seeder extends Seeder
{
    public function run()
    {
        $insertIfNotExists = function($table, $uniqueField, $data) {
            $exists = $this->db->table($table)->where($uniqueField, $data[$uniqueField])->get()->getRow();
            if (!$exists) { $this->db->table($table)->insert($data); return true; }
            return false;
        };

        // ============ MATA PELAJARAN ============
        $mapelData = [
            ['kode_mapel' => 'MTK',    'nama_mapel' => 'Matematika',           'kelompok' => 'wajib'],
            ['kode_mapel' => 'BIN',    'nama_mapel' => 'Bahasa Indonesia',     'kelompok' => 'wajib'],
            ['kode_mapel' => 'BING',   'nama_mapel' => 'Bahasa Inggris',       'kelompok' => 'wajib'],
            ['kode_mapel' => 'IPA',    'nama_mapel' => 'Ilmu Pengetahuan Alam','kelompok' => 'wajib'],
            ['kode_mapel' => 'IPS',    'nama_mapel' => 'Ilmu Pengetahuan Sosial','kelompok' => 'wajib'],
            ['kode_mapel' => 'PKN',    'nama_mapel' => 'Pendidikan Kewarganegaraan','kelompok' => 'wajib'],
            ['kode_mapel' => 'PAI',    'nama_mapel' => 'Pendidikan Agama Islam','kelompok' => 'keagamaan'],
            ['kode_mapel' => 'ARAB',   'nama_mapel' => 'Bahasa Arab',          'kelompok' => 'keagamaan'],
            ['kode_mapel' => 'FIQH',   'nama_mapel' => 'Fiqih',               'kelompok' => 'keagamaan'],
            ['kode_mapel' => 'AQIDAH','nama_mapel' => 'Aqidah Akhlak',       'kelompok' => 'keagamaan'],
            ['kode_mapel' => 'QURAN',  'nama_mapel' => 'Al-Quran Hadist',     'kelompok' => 'keagamaan'],
            ['kode_mapel' => 'SKI',    'nama_mapel' => 'Sejarah Kebudayaan Islam','kelompok' => 'keagamaan'],
            ['kode_mapel' => 'PJOK',   'nama_mapel' => 'Pendidikan Jasmani',  'kelompok' => 'wajib'],
            ['kode_mapel' => 'SENI',   'nama_mapel' => 'Seni Budaya',         'kelompok' => 'muatan_lokal'],
            ['kode_mapel' => 'TIK',    'nama_mapel' => 'Teknologi Informasi', 'kelompok' => 'muatan_lokal'],
            ['kode_mapel' => 'TAHFIDZ','nama_mapel' => 'Tahfidzul Quran',    'kelompok' => 'keagamaan'],
        ];
        $cnt = 0;
        foreach ($mapelData as $m) { if ($insertIfNotExists('mata_pelajaran', 'kode_mapel', $m)) $cnt++; }
        echo "✅ Seeded: {$cnt} Mata Pelajaran\n";

        // ============ SAMPLE PEGAWAI (20 guru + 5 staf) ============
        $guruNames = [
            ['nama_lengkap' => 'Ustadz Ahmad Fauzi, S.Pd.I', 'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap', 'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru PAI'],
            ['nama_lengkap' => 'Ustadzah Siti Aminah, S.Pd',  'jenis_kelamin' => 'P', 'status_kepegawaian' => 'Tetap', 'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru Matematika'],
            ['nama_lengkap' => 'Ustadz Muhammad Ridwan, M.Pd', 'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap', 'pendidikan_terakhir' => 'S2', 'jabatan' => 'Guru Bahasa Arab'],
            ['nama_lengkap' => 'Ustadzah Fatimah Zahra, S.Pd', 'jenis_kelamin' => 'P', 'status_kepegawaian' => 'Tetap', 'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru B. Indonesia'],
            ['nama_lengkap' => 'Ustadz Khalid Ibrahim, S.Si',  'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap', 'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru IPA'],
            ['nama_lengkap' => 'Ustadzah Maryam Hasan, S.Pd',  'jenis_kelamin' => 'P', 'status_kepegawaian' => 'GTT',  'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru B. Inggris'],
            ['nama_lengkap' => 'Ustadz Bilal Saputra, S.Pd.I', 'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru Fiqih'],
            ['nama_lengkap' => 'Ustadz Umar Pratama, M.Ag',    'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S2', 'jabatan' => 'Guru Aqidah'],
            ['nama_lengkap' => 'Ustadzah Khadijah Wulan, S.Pd','jenis_kelamin' => 'P', 'status_kepegawaian' => 'GTT',  'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru IPS'],
            ['nama_lengkap' => 'Ustadz Hamzah Firmansyah',     'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Honorer','pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru PJOK'],
            ['nama_lengkap' => 'Ustadz Yusuf Rahman, S.Kom',   'jenis_kelamin' => 'L', 'status_kepegawaian' => 'GTT',  'pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru TIK'],
            ['nama_lengkap' => 'Ustadzah Aisyah Permata, S.Pd','jenis_kelamin' => 'P', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru Seni'],
            ['nama_lengkap' => 'Ustadz Zaid Hafiz, S.Pd.I',    'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru Tahfidz'],
            ['nama_lengkap' => 'Ustadz Ali Hidayat, Lc',        'jenis_kelamin' => 'L', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S1', 'jabatan' => 'Guru Al-Quran'],
            ['nama_lengkap' => 'Ustadzah Ruqayyah Aini, M.Pd', 'jenis_kelamin' => 'P', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S2', 'jabatan' => 'Guru SKI'],
            // Staf
            ['nama_lengkap' => 'Bapak Surya Darma',        'jenis_kelamin' => 'L', 'status_kepegawaian' => 'PTT',  'pendidikan_terakhir' => 'SMA', 'jabatan' => 'Staf TU'],
            ['nama_lengkap' => 'Ibu Dewi Lestari, S.E',    'jenis_kelamin' => 'P', 'status_kepegawaian' => 'Tetap','pendidikan_terakhir' => 'S1',  'jabatan' => 'Bendahara'],
            ['nama_lengkap' => 'Bapak Eko Prasetyo',        'jenis_kelamin' => 'L', 'status_kepegawaian' => 'PTT',  'pendidikan_terakhir' => 'SMA', 'jabatan' => 'Satpam'],
            ['nama_lengkap' => 'Ibu Wati Suryani',          'jenis_kelamin' => 'P', 'status_kepegawaian' => 'Honorer','pendidikan_terakhir' => 'SMA','jabatan' => 'Kebersihan'],
            ['nama_lengkap' => 'Bapak Hendra Gunawan, S.Kom','jenis_kelamin' => 'L','status_kepegawaian' => 'GTT', 'pendidikan_terakhir' => 'S1',  'jabatan' => 'IT Support'],
        ];
        $cnt = 0;
        foreach ($guruNames as $g) {
            $g['tanggal_bergabung'] = date('Y-m-d', strtotime('-' . mt_rand(1, 8) . ' years'));
            $g['is_active'] = 1;
            if ($insertIfNotExists('pegawai', 'nama_lengkap', $g)) $cnt++;
        }
        echo "✅ Seeded: {$cnt} Pegawai\n";

        // ============ TAHUN AJARAN ============
        $taData = [
            ['kode' => '2024/2025-1', 'nama' => 'Tahun Ajaran 2024/2025 Ganjil',  'semester' => 'ganjil', 'tanggal_mulai' => '2024-07-15', 'tanggal_selesai' => '2024-12-20', 'is_active' => 0],
            ['kode' => '2024/2025-2', 'nama' => 'Tahun Ajaran 2024/2025 Genap',   'semester' => 'genap',  'tanggal_mulai' => '2025-01-06', 'tanggal_selesai' => '2025-06-20', 'is_active' => 0],
            ['kode' => '2025/2026-1', 'nama' => 'Tahun Ajaran 2025/2026 Ganjil',  'semester' => 'ganjil', 'tanggal_mulai' => '2025-07-14', 'tanggal_selesai' => '2025-12-19', 'is_active' => 0],
            ['kode' => '2025/2026-2', 'nama' => 'Tahun Ajaran 2025/2026 Genap',   'semester' => 'genap',  'tanggal_mulai' => '2026-01-05', 'tanggal_selesai' => '2026-06-19', 'is_active' => 1],
        ];
        $cnt = 0;
        foreach ($taData as $t) { if ($insertIfNotExists('tahun_ajaran', 'kode', $t)) $cnt++; }
        echo "✅ Seeded: {$cnt} Tahun Ajaran\n";

        // ============ NEW PERMISSIONS ============
        $newPerms = [
            ['code' => 'pegawai.create',         'module' => 'pegawai',        'action' => 'create',  'description' => 'Tambah data pegawai'],
            ['code' => 'pegawai.read',           'module' => 'pegawai',        'action' => 'read',    'description' => 'Lihat data pegawai'],
            ['code' => 'pegawai.update',         'module' => 'pegawai',        'action' => 'update',  'description' => 'Edit data pegawai'],
            ['code' => 'pegawai.delete',         'module' => 'pegawai',        'action' => 'delete',  'description' => 'Hapus data pegawai'],
            ['code' => 'presensi_pegawai.manage','module' => 'presensi_pegawai','action' => 'manage', 'description' => 'Kelola presensi pegawai'],
            ['code' => 'mapel.manage',           'module' => 'mapel',          'action' => 'manage',  'description' => 'Kelola mata pelajaran'],
            ['code' => 'presensi_siswa.create',  'module' => 'presensi_siswa', 'action' => 'create',  'description' => 'Input presensi siswa'],
            ['code' => 'presensi_siswa.read',    'module' => 'presensi_siswa', 'action' => 'read',    'description' => 'Lihat presensi siswa'],
            ['code' => 'jadwal.manage',          'module' => 'jadwal',         'action' => 'manage',  'description' => 'Kelola jadwal pelajaran'],
            ['code' => 'kalender.manage',        'module' => 'kalender',       'action' => 'manage',  'description' => 'Kelola kalender akademik'],
        ];
        $cnt = 0;
        foreach ($newPerms as $p) { if ($insertIfNotExists('permissions', 'code', $p)) $cnt++; }
        echo "✅ Seeded: {$cnt} new Permissions\n";

        // Assign all to super_admin
        $allPerms = $this->db->table('permissions')->select('id')->get()->getResultArray();
        $assigned = 0;
        foreach ($allPerms as $perm) {
            $exists = $this->db->table('role_permissions')->where('role_id', 1)->where('permission_id', $perm['id'])->get()->getRow();
            if (!$exists) { $this->db->table('role_permissions')->insert(['role_id' => 1, 'permission_id' => $perm['id']]); $assigned++; }
        }
        echo "✅ Assigned: {$assigned} new permissions to super_admin\n";
    }
}
