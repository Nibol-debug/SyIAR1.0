<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Insert roles
        $roles = [
            ['nama_role' => 'super_admin', 'deskripsi' => 'Akses penuh ke semua fitur'],
            ['nama_role' => 'admin_akademik', 'deskripsi' => 'Mengelola data akademik dan penilaian'],
            ['nama_role' => 'guru', 'deskripsi' => 'Input penilaian dan melihat siswa'],
            ['nama_role' => 'kepala_bagian', 'deskripsi' => 'Melihat laporan dan rekap'],
        ];
        
        foreach ($roles as $role) {
            $this->db->table('roles')->insert($role);
        }
        
        // Insert permissions
        $permissions = [
            // User management
            ['kode' => 'user.create', 'modul' => 'user', 'aksi' => 'create', 'deskripsi' => 'Membuat user baru'],
            ['kode' => 'user.read', 'modul' => 'user', 'aksi' => 'read', 'deskripsi' => 'Melihat data user'],
            ['kode' => 'user.update', 'modul' => 'user', 'aksi' => 'update', 'deskripsi' => 'Mengedit user'],
            ['kode' => 'user.delete', 'modul' => 'user', 'aksi' => 'delete', 'deskripsi' => 'Menghapus user'],
            
            // Role management
            ['kode' => 'role.manage', 'modul' => 'role', 'aksi' => 'manage', 'deskripsi' => 'Kelola role & permission'],
            
            // Assessment
            ['kode' => 'penilaian.create', 'modul' => 'penilaian', 'aksi' => 'create', 'deskripsi' => 'Input nilai'],
            ['kode' => 'penilaian.read', 'modul' => 'penilaian', 'aksi' => 'read', 'deskripsi' => 'Lihat penilaian'],
            ['kode' => 'penilaian.update', 'modul' => 'penilaian', 'aksi' => 'update', 'deskripsi' => 'Edit nilai'],
            ['kode' => 'penilaian.delete', 'modul' => 'penilaian', 'aksi' => 'delete', 'deskripsi' => 'Hapus nilai'],
            ['kode' => 'penilaian.export', 'modul' => 'penilaian', 'aksi' => 'export', 'deskripsi' => 'Export data penilaian'],
            
            // Master data
            ['kode' => 'master.santri.create', 'modul' => 'master', 'aksi' => 'create', 'deskripsi' => 'Tambah santri'],
            ['kode' => 'master.santri.read', 'modul' => 'master', 'aksi' => 'read', 'deskripsi' => 'Lihat santri'],
            ['kode' => 'master.santri.update', 'modul' => 'master', 'aksi' => 'update', 'deskripsi' => 'Edit santri'],
            ['kode' => 'master.santri.delete', 'modul' => 'master', 'aksi' => 'delete', 'deskripsi' => 'Hapus santri'],
            ['kode' => 'master.aspek.manage', 'modul' => 'master', 'aksi' => 'manage', 'deskripsi' => 'Kelola aspek penilaian'],
        ];
        
        foreach ($permissions as $perm) {
            $this->db->table('permissions')->insert($perm);
        }
        
        // Assign permissions to super_admin (role_id = 1)
        $permissionIds = $this->db->table('permissions')->select('id')->get()->getResultArray();
        foreach ($permissionIds as $perm) {
            $this->db->table('role_permissions')->insert([
                'role_id' => 1,
                'permission_id' => $perm['id']
            ]);
        }
        
        // Create super admin user
        $faker = Factory::create('id_ID');
        
        $userData = [
            'username' => 'superadmin',
            'email' => 'admin@syiar.com',
            'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Super Administrator',
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('users')->insert($userData);
        $userId = $this->db->insertID();
        
        // Assign super_admin role to user
        $this->db->table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => 1
        ]);
        
        // Create sample guru
        $guruData = [
            'username' => 'guru1',
            'email' => 'guru1@syiar.com',
            'password_hash' => password_hash('Guru123!', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Ahmad Fauzi, S.Pd',
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('users')->insert($guruData);
        $guruId = $this->db->insertID();
        
        // Assign guru role (role_id = 3)
        $this->db->table('user_roles')->insert([
            'user_id' => $guruId,
            'role_id' => 3
        ]);
        
        echo "Seeding completed successfully!\n";
        echo "Super Admin credentials:\n";
        echo "Username: superadmin\n";
        echo "Password: Admin123!\n";
        echo "\nGuru credentials:\n";
        echo "Username: guru1\n";
        echo "Password: Guru123!\n";
    }
}