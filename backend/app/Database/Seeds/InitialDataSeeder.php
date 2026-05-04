<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Insert roles jika belum ada
        $roles = [
            ['nama_role' => 'super_admin', 'deskripsi' => 'Akses penuh ke semua fitur'],
            ['nama_role' => 'admin_akademik', 'deskripsi' => 'Mengelola data akademik dan penilaian'],
            ['nama_role' => 'guru', 'deskripsi' => 'Input penilaian dan melihat siswa'],
            ['nama_role' => 'kepala_bagian', 'deskripsi' => 'Melihat laporan dan rekap'],
        ];
        
        foreach ($roles as $role) {
            $exists = $this->db->table('roles')
                ->where('nama_role', $role['nama_role'])
                ->get()
                ->getRow();
            
            if (!$exists) {
                $this->db->table('roles')->insert($role);
                echo "Role '{$role['nama_role']} ditambahkan\n";
            } else {
                echo "Role '{$role['nama_role']}' sudah ada, skip\n";
            }
        }
        
        // Insert permissions jika belum ada
        $permissions = [
            ['kode' => 'user.create', 'modul' => 'user', 'aksi' => 'create', 'deskripsi' => 'Membuat user baru'],
            ['kode' => 'user.read', 'modul' => 'user', 'aksi' => 'read', 'deskripsi' => 'Melihat data user'],
            ['kode' => 'user.update', 'modul' => 'user', 'aksi' => 'update', 'deskripsi' => 'Mengedit user'],
            ['kode' => 'user.delete', 'modul' => 'user', 'aksi' => 'delete', 'deskripsi' => 'Menghapus user'],
            ['kode' => 'role.manage', 'modul' => 'role', 'aksi' => 'manage', 'deskripsi' => 'Kelola role & permission'],
            ['kode' => 'penilaian.create', 'modul' => 'penilaian', 'aksi' => 'create', 'deskripsi' => 'Input nilai'],
            ['kode' => 'penilaian.read', 'modul' => 'penilaian', 'aksi' => 'read', 'deskripsi' => 'Lihat penilaian'],
            ['kode' => 'penilaian.update', 'modul' => 'penilaian', 'aksi' => 'update', 'deskripsi' => 'Edit nilai'],
            ['kode' => 'penilaian.delete', 'modul' => 'penilaian', 'aksi' => 'delete', 'deskripsi' => 'Hapus nilai'],
            ['kode' => 'penilaian.export', 'modul' => 'penilaian', 'aksi' => 'export', 'deskripsi' => 'Export data penilaian'],
            ['kode' => 'master.santri.create', 'modul' => 'master', 'aksi' => 'create', 'deskripsi' => 'Tambah santri'],
            ['kode' => 'master.santri.read', 'modul' => 'master', 'aksi' => 'read', 'deskripsi' => 'Lihat santri'],
            ['kode' => 'master.santri.update', 'modul' => 'master', 'aksi' => 'update', 'deskripsi' => 'Edit santri'],
            ['kode' => 'master.santri.delete', 'modul' => 'master', 'aksi' => 'delete', 'deskripsi' => 'Hapus santri'],
            ['kode' => 'master.aspek.manage', 'modul' => 'master', 'aksi' => 'manage', 'deskripsi' => 'Kelola aspek penilaian'],
        ];
        
        foreach ($permissions as $perm) {
            $exists = $this->db->table('permissions')
                ->where('kode', $perm['kode'])
                ->get()
                ->getRow();
            
            if (!$exists) {
                $this->db->table('permissions')->insert($perm);
                echo "Permission '{$perm['kode']}' ditambahkan\n";
            } else {
                echo "Permission '{$perm['kode']}' sudah ada, skip\n";
            }
        }
        
        // Assign semua permission ke super_admin (role_id = 1)
        $superAdminRole = $this->db->table('roles')
            ->where('nama_role', 'super_admin')
            ->get()
            ->getRow();
        
        if ($superAdminRole) {
            $allPermissions = $this->db->table('permissions')->get()->getResult();
            
            foreach ($allPermissions as $perm) {
                $exists = $this->db->table('role_permissions')
                    ->where('role_id', $superAdminRole->id)
                    ->where('permission_id', $perm->id)
                    ->get()
                    ->getRow();
                
                if (!$exists) {
                    $this->db->table('role_permissions')->insert([
                        'role_id' => $superAdminRole->id,
                        'permission_id' => $perm->id
                    ]);
                }
            }
            echo "Permission assigned to super_admin\n";
        }
        
        // Buat user superadmin jika belum ada
        $userExists = $this->db->table('users')
            ->where('username', 'superadmin')
            ->get()
            ->getRow();
        
        if (!$userExists) {
            $this->db->table('users')->insert([
                'username' => 'superadmin',
                'email' => 'admin@syiar.com',
                'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Super Administrator',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $userId = $this->db->insertID();
            
            // Assign role super_admin ke user
            if ($superAdminRole) {
                $this->db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $superAdminRole->id
                ]);
            }
            echo "User superadmin created\n";
        } else {
            echo "User superadmin already exists, skip\n";
        }
        
        echo "Seeding completed!\n";
    }
}
