<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Helper to insert if not exists
        $insertIfNotExists = function($table, $uniqueField, $data) {
            $exists = $this->db->table($table)->where($uniqueField, $data[$uniqueField])->get()->getRow();
            if (!$exists) {
                $this->db->table($table)->insert($data);
                return true;
            }
            return false;
        };

        // 1. SEED ROLES
        $roles = [
            ['name' => 'Super Admin',      'slug' => 'super_admin',      'description' => 'Akses penuh ke semua fitur sistem', 'is_active' => 1],
            ['name' => 'Admin Akademik',   'slug' => 'admin_akademik',   'description' => 'Mengelola data akademik & penilaian', 'is_active' => 1],
            ['name' => 'Guru',             'slug' => 'guru',             'description' => 'Input penilaian & melihat data santri', 'is_active' => 1],
            ['name' => 'Kepala Bagian',    'slug' => 'kepala_bagian',    'description' => 'Melihat laporan & rekap data', 'is_active' => 1],
        ];
        $cnt = 0;
        foreach ($roles as $r) { if ($insertIfNotExists('roles', 'slug', $r)) $cnt++; }
        echo "✅ Seeded: {$cnt} new Roles\n";

        // 2. SEED PERMISSIONS
        $permissions = [
            ['code' => 'user.create',       'module' => 'user',       'action' => 'create', 'description' => 'Membuat user baru'],
            ['code' => 'user.read',         'module' => 'user',       'action' => 'read',   'description' => 'Melihat data user'],
            ['code' => 'user.update',       'module' => 'user',       'action' => 'update', 'description' => 'Mengedit user'],
            ['code' => 'user.delete',       'module' => 'user',       'action' => 'delete', 'description' => 'Menghapus user'],
            ['code' => 'role.manage',       'module' => 'role',       'action' => 'manage', 'description' => 'Kelola role & permission'],
            ['code' => 'penilaian.create',  'module' => 'penilaian',  'action' => 'create', 'description' => 'Input nilai santri'],
            ['code' => 'penilaian.read',    'module' => 'penilaian',  'action' => 'read',   'description' => 'Lihat rekap penilaian'],
            ['code' => 'penilaian.update',  'module' => 'penilaian',  'action' => 'update', 'description' => 'Edit nilai'],
            ['code' => 'penilaian.delete',  'module' => 'penilaian',  'action' => 'delete', 'description' => 'Hapus nilai'],
            ['code' => 'penilaian.export',  'module' => 'penilaian',  'action' => 'export', 'description' => 'Export data penilaian'],
            ['code' => 'santri.create',     'module' => 'santri',     'action' => 'create', 'description' => 'Tambah data santri'],
            ['code' => 'santri.read',       'module' => 'santri',     'action' => 'read',   'description' => 'Lihat data santri'],
            ['code' => 'santri.update',     'module' => 'santri',     'action' => 'update', 'description' => 'Edit data santri'],
            ['code' => 'santri.delete',     'module' => 'santri',     'action' => 'delete', 'description' => 'Hapus data santri'],
        ];
        $cnt = 0;
        foreach ($permissions as $p) { if ($insertIfNotExists('permissions', 'code', $p)) $cnt++; }
        echo "✅ Seeded: {$cnt} new Permissions\n";

        // 3. SEED USERS (Password: Admin123! & Guru123!)
        $adminPass = password_hash('Admin123!', PASSWORD_DEFAULT);
        $guruPass  = password_hash('Guru123!', PASSWORD_DEFAULT);

        $users = [
            ['username' => 'superadmin', 'email' => 'superadmin@syiar.id', 'password' => $adminPass, 'nama_lengkap' => 'Administrator Utama', 'is_active' => 1],
            ['username' => 'guru1',      'email' => 'guru1@syiar.id',      'password' => $guruPass,  'nama_lengkap' => 'Guru Contoh', 'is_active' => 1],
        ];
        $cnt = 0;
        foreach ($users as $u) { if ($insertIfNotExists('users', 'username', $u)) $cnt++; }
        echo "✅ Seeded: {$cnt} new Users\n";

        // 4. MAP USERS TO ROLES
        $userRoles = [
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 2, 'role_id' => 3],
        ];
        $cnt = 0;
        foreach ($userRoles as $ur) {
            $exists = $this->db->table('user_roles')
                ->where('user_id', $ur['user_id'])
                ->where('role_id', $ur['role_id'])
                ->get()->getRow();
            if (!$exists) { $this->db->table('user_roles')->insert($ur); $cnt++; }
        }
        echo "✅ Seeded: {$cnt} new User-Role Mappings\n";

        // 5. MAP SUPER_ADMIN TO ALL PERMISSIONS
        $allPerms = $this->db->table('permissions')->select('id')->get()->getResultArray();
        $cnt = 0;
        foreach ($allPerms as $perm) {
            $exists = $this->db->table('role_permissions')
                ->where('role_id', 1)
                ->where('permission_id', $perm['id'])
                ->get()->getRow();
            if (!$exists) {
                $this->db->table('role_permissions')->insert(['role_id' => 1, 'permission_id' => $perm['id']]);
                $cnt++;
            }
        }
        echo "✅ Seeded: {$cnt} new Role-Permission Mappings\n";
    }
}
