<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixAnswersSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('ujian_jawaban')->where('sesi_id', 1)->where('soal_id', 1)->update([
            'jawaban_siswa' => 'Fardhu Ain',
            'is_benar' => 1
        ]);
        $this->db->table('ujian_jawaban')->where('sesi_id', 1)->where('soal_id', 2)->update([
            'jawaban_siswa' => "Sama'",
            'is_benar' => 1
        ]);
        $this->db->table('ujian_sesi')->where('id', 1)->update([
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s')
        ]);
    }
}
