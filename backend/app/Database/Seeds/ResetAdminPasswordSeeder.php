<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResetAdminPasswordSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->where('username', 'superadmin')->update([
            'password' => password_hash('admin123', PASSWORD_DEFAULT)
        ]);
    }
}
