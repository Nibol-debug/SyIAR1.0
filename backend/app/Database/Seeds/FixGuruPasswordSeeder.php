<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixGuruPasswordSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->where('username', 'guru1')->update([
            'password' => password_hash('guru123', PASSWORD_DEFAULT)
        ]);
    }
}
