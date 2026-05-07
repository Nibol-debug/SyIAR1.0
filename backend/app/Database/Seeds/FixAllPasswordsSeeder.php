<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk menyetel ulang semua password akun ke nilai yang benar
 * sesuai dengan yang ditampilkan di halaman login.
 */
class FixAllPasswordsSeeder extends Seeder
{
    public function run()
    {
        // superadmin / Admin123!
        $this->db->table('users')->where('username', 'superadmin')->update([
            'password' => password_hash('Admin123!', PASSWORD_DEFAULT),
            'is_active' => 1
        ]);
        
        // guru1 / Guru123!
        $this->db->table('users')->where('username', 'guru1')->update([
            'password' => password_hash('Guru123!', PASSWORD_DEFAULT),
            'is_active' => 1
        ]);
        
        echo "✅ Semua password berhasil direset:\n";
        echo "   - superadmin / Admin123!\n";
        echo "   - guru1 / Guru123!\n";
    }
}
