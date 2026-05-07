<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixMapelAspekSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('mata_pelajaran')->where('id', 1)->update(['aspek_id' => 7]);
        $this->db->table('mata_pelajaran')->where('id', 8)->update(['aspek_id' => 8]);
    }
}
