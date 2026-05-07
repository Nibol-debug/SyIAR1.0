<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CheckPenilaianSeeder extends Seeder
{
    public function run()
    {
        $res = $this->db->table('penilaian')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
        print_r($res);
    }
}
