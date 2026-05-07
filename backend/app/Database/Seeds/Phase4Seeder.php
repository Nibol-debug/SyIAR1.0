<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase4Seeder extends Seeder
{
    public function run()
    {
        // Dummy Bank Soal
        $bankSoalData = [
            [
                'mapel_id' => 1, // Assuming mapel_id 1 is Matematika/Aqidah etc.
                'guru_id' => 1,  // Assuming pegawai_id 1 exists
                'pertanyaan' => 'Apa hukum mempelajari ilmu tauhid?',
                'tipe_soal' => 'pg',
                'pilihan_jawaban' => json_encode(['Fardhu Ain', 'Fardhu Kifayah', 'Sunnah Muakkad', 'Mubah']),
                'kunci_jawaban' => 'Fardhu Ain',
                'tingkat_kesulitan' => 'mudah',
                'topik' => 'Dasar Tauhid',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'mapel_id' => 1,
                'guru_id' => 1,
                'pertanyaan' => 'Sifat wajib Allah SWT yang berarti Maha Mendengar adalah...',
                'tipe_soal' => 'pg',
                'pilihan_jawaban' => json_encode(['Sama\'', 'Bashar', 'Kalam', 'Ilmu']),
                'kunci_jawaban' => 'Sama\'',
                'tingkat_kesulitan' => 'mudah',
                'topik' => 'Sifat Wajib Allah',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'mapel_id' => 1,
                'guru_id' => 1,
                'pertanyaan' => 'Sebutkan 3 rukun Islam secara berurutan!',
                'tipe_soal' => 'esai',
                'pilihan_jawaban' => null,
                'kunci_jawaban' => 'Syahadat, Shalat, Zakat',
                'tingkat_kesulitan' => 'sedang',
                'topik' => 'Rukun Islam',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Ensure foreign keys exist. Since we don't know the exact IDs from Phase 2 and 3,
        // we'll fetch an existing mapel and pegawai to attach.
        $db = \Config\Database::connect();
        $mapel = $db->table('mata_pelajaran')->get()->getRow();
        $guru = $db->table('pegawai')->get()->getRow();
        $kelas = $db->table('kelas')->get()->getRow();

        if ($mapel && $guru && $kelas) {
            foreach ($bankSoalData as &$soal) {
                $soal['mapel_id'] = $mapel->id;
                $soal['guru_id'] = $guru->id;
            }
            $this->db->table('bank_soal')->insertBatch($bankSoalData);
            
            $insertedSoalIds = [];
            $soalRecords = $this->db->table('bank_soal')->select('id')->get()->getResult();
            foreach($soalRecords as $r) {
                $insertedSoalIds[] = $r->id;
            }

            // Dummy Ujian
            $ujianData = [
                'judul' => 'Ujian Akhir Semester Ganjil - Tauhid',
                'mapel_id' => $mapel->id,
                'kelas_ids' => json_encode([$kelas->id]),
                'guru_id' => $guru->id,
                'durasi_menit' => 60,
                'jumlah_soal' => 3,
                'tanggal_mulai' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'tanggal_selesai' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'token_akses' => 'UAS123',
                'shuffle_soal' => true,
                'shuffle_jawaban' => true,
                'max_peringatan' => 3,
                'status' => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->table('ujian')->insert($ujianData);
            $ujianId = $this->db->insertID();

            // Dummy Ujian Soal (Linking)
            $ujianSoalData = [];
            $urutan = 1;
            foreach($insertedSoalIds as $soalId) {
                $ujianSoalData[] = [
                    'ujian_id' => $ujianId,
                    'soal_id' => $soalId,
                    'urutan' => $urutan++,
                    'bobot_nilai' => ($urutan == 4) ? 2.0 : 1.0, // Esai has higher weight
                ];
            }
            $this->db->table('ujian_soal')->insertBatch($ujianSoalData);
        }
    }
}
