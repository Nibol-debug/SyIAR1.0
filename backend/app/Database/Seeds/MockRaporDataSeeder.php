<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MockRaporDataSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('penilaian')->insert([
            'santri_id' => 1,
            'aspek_id' => 7,
            'guru_id' => 1,
            'nilai' => 80.00,
            'jenis_nilai' => 'harian',
            'tanggal_penilaian' => date('Y-m-d'),
            'periode' => date('Y-m'),
            'is_draft' => 0
        ]);
        $this->db->table('penilaian')->insert([
            'santri_id' => 1,
            'aspek_id' => 7,
            'guru_id' => 1,
            'nilai' => 75.00,
            'jenis_nilai' => 'uts',
            'tanggal_penilaian' => date('Y-m-d'),
            'periode' => date('Y-m'),
            'is_draft' => 0
        ]);
    }
}
