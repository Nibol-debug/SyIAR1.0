<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase3Seeder extends Seeder
{
    public function run()
    {
        // Helper: insert if not exists
        $insertIfNotExists = function($table, $uniqueField, $data) {
            $exists = $this->db->table($table)->where($uniqueField, $data[$uniqueField])->get()->getRow();
            if (!$exists) {
                $this->db->table($table)->insert($data);
                return true;
            }
            return false;
        };

        // ============ KELAS (24 kelas) ============
        $kelasData = [
            // SD
            ['nama_kelas' => '1A', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '1B', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '2A', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '2B', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '3A', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '3B', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '4A', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '4B', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '5A', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '5B', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '6A', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            ['nama_kelas' => '6B', 'jurusan' => 'SD', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 30, 'is_active' => 1],
            // SMP
            ['nama_kelas' => '7A', 'jurusan' => 'Reguler', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 32, 'is_active' => 1],
            ['nama_kelas' => '7B', 'jurusan' => 'Reguler', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 32, 'is_active' => 1],
            ['nama_kelas' => '8A', 'jurusan' => 'Reguler', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 32, 'is_active' => 1],
            ['nama_kelas' => '8B', 'jurusan' => 'Reguler', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 32, 'is_active' => 1],
            ['nama_kelas' => '9A', 'jurusan' => 'Reguler', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 32, 'is_active' => 1],
            ['nama_kelas' => '9B', 'jurusan' => 'Reguler', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 32, 'is_active' => 1],
            // SMA
            ['nama_kelas' => '10A', 'jurusan' => 'IPA', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 35, 'is_active' => 1],
            ['nama_kelas' => '10B', 'jurusan' => 'IPS', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 35, 'is_active' => 1],
            ['nama_kelas' => '11A', 'jurusan' => 'IPA', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 35, 'is_active' => 1],
            ['nama_kelas' => '11B', 'jurusan' => 'IPS', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 35, 'is_active' => 1],
            ['nama_kelas' => '12A', 'jurusan' => 'IPA', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 35, 'is_active' => 1],
            ['nama_kelas' => '12B', 'jurusan' => 'IPS', 'tahun_ajaran' => '2025/2026', 'kapasitas' => 35, 'is_active' => 1],
        ];
        $kelasInserted = 0;
        foreach ($kelasData as $k) {
            if ($insertIfNotExists('kelas', 'nama_kelas', $k)) $kelasInserted++;
        }
        echo "✅ Seeded: {$kelasInserted} Kelas (total: " . count($kelasData) . ")\n";

        // ============ KATEGORI PENILAIAN ============
        $kategoriData = [
            ['nama_kategori' => 'Akhlak & Karakter',  'kode_kategori' => 'AKHLAK',   'deskripsi' => 'Penilaian sikap dan perilaku',            'warna' => '#10b981', 'urutan' => 1, 'is_active' => 1],
            ['nama_kategori' => 'Tahfidz Al-Quran',   'kode_kategori' => 'TAHFIDZ',  'deskripsi' => 'Penilaian hafalan Quran',                  'warna' => '#3b82f6', 'urutan' => 2, 'is_active' => 1],
            ['nama_kategori' => 'Akademik',            'kode_kategori' => 'AKADEMIK', 'deskripsi' => 'Penilaian mata pelajaran',                 'warna' => '#8b5cf6', 'urutan' => 3, 'is_active' => 1],
            ['nama_kategori' => 'Ekstrakurikuler',     'kode_kategori' => 'EKSKUL',   'deskripsi' => 'Penilaian kegiatan ekstrakurikuler',       'warna' => '#f59e0b', 'urutan' => 4, 'is_active' => 1],
        ];
        $katInserted = 0;
        foreach ($kategoriData as $k) {
            if ($insertIfNotExists('kategori_penilaian', 'kode_kategori', $k)) $katInserted++;
        }
        echo "✅ Seeded: {$katInserted} Kategori Penilaian\n";

        // ============ ASPEK PENILAIAN ============
        $aspekData = [
            ['kategori_id' => 1, 'nama_aspek' => 'Sholat Berjamaah',   'kode_aspek' => 'AKHLAK.SHOLAT',   'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.5, 'tipe_input' => 'range',  'options' => null, 'urutan' => 1, 'is_active' => 1],
            ['kategori_id' => 1, 'nama_aspek' => 'Sikap Sopan Santun', 'kode_aspek' => 'AKHLAK.SOPAN',    'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.0, 'tipe_input' => 'range',  'options' => null, 'urutan' => 2, 'is_active' => 1],
            ['kategori_id' => 1, 'nama_aspek' => 'Kedisiplinan',       'kode_aspek' => 'AKHLAK.DISIPLIN', 'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.2, 'tipe_input' => 'range',  'options' => null, 'urutan' => 3, 'is_active' => 1],
            ['kategori_id' => 2, 'nama_aspek' => 'Hafalan Juz 30',     'kode_aspek' => 'TAHFIDZ.JUZ30',   'skala_min' => 0, 'skala_max' => 100, 'bobot' => 2.0, 'tipe_input' => 'number', 'options' => null, 'urutan' => 1, 'is_active' => 1],
            ['kategori_id' => 2, 'nama_aspek' => 'Tajwid',             'kode_aspek' => 'TAHFIDZ.TAJWID',  'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.5, 'tipe_input' => 'select', 'options' => '{"A":"Sangat Baik","B":"Baik","C":"Cukup","D":"Kurang"}', 'urutan' => 2, 'is_active' => 1],
            ['kategori_id' => 2, 'nama_aspek' => 'Makhraj',            'kode_aspek' => 'TAHFIDZ.MAKHRAJ', 'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.0, 'tipe_input' => 'range',  'options' => null, 'urutan' => 3, 'is_active' => 1],
            ['kategori_id' => 3, 'nama_aspek' => 'Matematika',         'kode_aspek' => 'AKADEMIK.MTK',    'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.0, 'tipe_input' => 'number', 'options' => null, 'urutan' => 1, 'is_active' => 1],
            ['kategori_id' => 3, 'nama_aspek' => 'Bahasa Arab',        'kode_aspek' => 'AKADEMIK.ARAB',   'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.0, 'tipe_input' => 'number', 'options' => null, 'urutan' => 2, 'is_active' => 1],
            ['kategori_id' => 3, 'nama_aspek' => 'Bahasa Inggris',     'kode_aspek' => 'AKADEMIK.ENG',    'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.0, 'tipe_input' => 'number', 'options' => null, 'urutan' => 3, 'is_active' => 1],
            ['kategori_id' => 3, 'nama_aspek' => 'IPA / Sains',        'kode_aspek' => 'AKADEMIK.IPA',    'skala_min' => 0, 'skala_max' => 100, 'bobot' => 1.0, 'tipe_input' => 'number', 'options' => null, 'urutan' => 4, 'is_active' => 1],
            ['kategori_id' => 4, 'nama_aspek' => 'Pramuka',            'kode_aspek' => 'EKSKUL.PRAMUKA',  'skala_min' => 0, 'skala_max' => 100, 'bobot' => 0.5, 'tipe_input' => 'range',  'options' => null, 'urutan' => 1, 'is_active' => 1],
            ['kategori_id' => 4, 'nama_aspek' => 'Olahraga',           'kode_aspek' => 'EKSKUL.OR',       'skala_min' => 0, 'skala_max' => 100, 'bobot' => 0.5, 'tipe_input' => 'range',  'options' => null, 'urutan' => 2, 'is_active' => 1],
        ];
        $aspekInserted = 0;
        foreach ($aspekData as $a) {
            $exists = $this->db->table('aspek_penilaian')->where('kode_aspek', $a['kode_aspek'])->get()->getRow();
            if (!$exists) {
                $this->db->table('aspek_penilaian')->insert($a);
                $aspekInserted++;
            }
        }
        echo "✅ Seeded: {$aspekInserted} Aspek Penilaian\n";

        // ============ ADDITIONAL PERMISSIONS ============
        // Use correct column names matching the permissions table: code, module, action, description
        $newPermissions = [
            ['code' => 'santri.create',    'module' => 'santri',     'action' => 'create', 'description' => 'Menambah data santri'],
            ['code' => 'santri.read',      'module' => 'santri',     'action' => 'read',   'description' => 'Melihat data santri'],
            ['code' => 'santri.update',    'module' => 'santri',     'action' => 'update', 'description' => 'Mengedit data santri'],
            ['code' => 'santri.delete',    'module' => 'santri',     'action' => 'delete', 'description' => 'Menghapus data santri'],
            ['code' => 'santri.import',    'module' => 'santri',     'action' => 'import', 'description' => 'Import data santri via CSV'],
            ['code' => 'santri.export',    'module' => 'santri',     'action' => 'export', 'description' => 'Export data santri'],
            ['code' => 'kelas.manage',     'module' => 'kelas',      'action' => 'manage', 'description' => 'Kelola data kelas'],
            ['code' => 'kategori.manage',  'module' => 'kategori',   'action' => 'manage', 'description' => 'Kelola kategori penilaian'],
            ['code' => 'aspek.manage',     'module' => 'aspek',      'action' => 'manage', 'description' => 'Kelola aspek penilaian'],
            ['code' => 'penilaian.create', 'module' => 'penilaian',  'action' => 'create', 'description' => 'Input nilai santri'],
            ['code' => 'penilaian.read',   'module' => 'penilaian',  'action' => 'read',   'description' => 'Lihat rekap nilai'],
            ['code' => 'penilaian.export', 'module' => 'penilaian',  'action' => 'export', 'description' => 'Export laporan nilai'],
            ['code' => 'ppdb.read',        'module' => 'ppdb',       'action' => 'read',   'description' => 'Lihat pendaftaran PPDB'],
            ['code' => 'ppdb.verify',      'module' => 'ppdb',       'action' => 'verify', 'description' => 'Verifikasi pendaftaran PPDB'],
            ['code' => 'dashboard.view',   'module' => 'dashboard',  'action' => 'view',   'description' => 'Lihat dashboard statistik'],
        ];
        
        $permInserted = 0;
        foreach ($newPermissions as $perm) {
            if ($insertIfNotExists('permissions', 'code', $perm)) $permInserted++;
        }
        echo "✅ Seeded: {$permInserted} new Permissions\n";

        // ============ ASSIGN ALL PERMISSIONS TO SUPER_ADMIN ============
        $superAdminPerms = $this->db->table('permissions')->select('id')->get()->getResultArray();
        $assigned = 0;
        foreach ($superAdminPerms as $perm) {
            $exists = $this->db->table('role_permissions')
                ->where('role_id', 1)
                ->where('permission_id', $perm['id'])
                ->get()
                ->getRow();
            if (!$exists) {
                $this->db->table('role_permissions')->insert([
                    'role_id'       => 1, 
                    'permission_id' => $perm['id']
                ]);
                $assigned++;
            }
        }
        echo "✅ Assigned: {$assigned} new permissions to super_admin\n";

        // ============ ASSIGN GURU PERMISSIONS ============
        $guruPermCodes = ['santri.read', 'penilaian.create', 'penilaian.read', 'dashboard.view'];
        foreach ($guruPermCodes as $code) {
            $perm = $this->db->table('permissions')->where('code', $code)->get()->getRow();
            if ($perm) {
                $exists = $this->db->table('role_permissions')
                    ->where('role_id', 3)
                    ->where('permission_id', $perm->id)
                    ->get()
                    ->getRow();
                if (!$exists) {
                    $this->db->table('role_permissions')->insert([
                        'role_id'       => 3,
                        'permission_id' => $perm->id
                    ]);
                }
            }
        }
        echo "✅ Assigned: Guru permissions\n";
    }
}
