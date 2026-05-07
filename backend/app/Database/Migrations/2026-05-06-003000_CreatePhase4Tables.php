<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase4Tables extends Migration
{
    public function up()
    {
        // 1. bank_soal
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'mapel_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'guru_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'pertanyaan' => [
                'type' => 'TEXT',
            ],
            'tipe_soal' => [
                'type' => 'ENUM',
                'constraint' => ['pg', 'pg_kompleks', 'menjodohkan', 'isian', 'esai'],
                'default' => 'pg',
            ],
            'pilihan_jawaban' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'kunci_jawaban' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tingkat_kesulitan' => [
                'type' => 'ENUM',
                'constraint' => ['mudah', 'sedang', 'sulit'],
                'default' => 'sedang',
            ],
            'topik' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bank_soal', true);

        // 2. ujian
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'mapel_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'kelas_ids' => [
                'type' => 'JSON',
            ],
            'guru_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'durasi_menit' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 60,
            ],
            'jumlah_soal' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'tanggal_mulai' => [
                'type' => 'DATETIME',
            ],
            'tanggal_selesai' => [
                'type' => 'DATETIME',
            ],
            'token_akses' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'shuffle_soal' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'shuffle_jawaban' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'max_peringatan' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 3,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'aktif', 'selesai'],
                'default' => 'draft',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ujian', true);

        // 3. ujian_soal
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ujian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'soal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'bobot_nilai' => [
                'type' => 'FLOAT',
                'default' => 1.0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ujian_id', 'ujian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('soal_id', 'bank_soal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ujian_soal', true);

        // 4. ujian_sesi
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ujian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'santri_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'waktu_mulai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'waktu_selesai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['belum', 'mengerjakan', 'selesai', 'diskualifikasi'],
                'default' => 'belum',
            ],
            'peringatan_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ujian_id', 'ujian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('santri_id', 'santris', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ujian_sesi', true);

        // 5. ujian_jawaban
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sesi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'soal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'jawaban_siswa' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_benar' => [
                'type' => 'BOOLEAN',
                'null' => true,
            ],
            'nilai_manual' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'pengoreksi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sesi_id', 'ujian_sesi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('soal_id', 'bank_soal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pengoreksi_id', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ujian_jawaban', true);

        // 6. ujian_pelanggaran
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sesi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'waktu' => [
                'type' => 'DATETIME',
            ],
            'detail' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sesi_id', 'ujian_sesi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ujian_pelanggaran', true);
    }

    public function down()
    {
        $this->forge->dropTable('ujian_pelanggaran', true);
        $this->forge->dropTable('ujian_jawaban', true);
        $this->forge->dropTable('ujian_sesi', true);
        $this->forge->dropTable('ujian_soal', true);
        $this->forge->dropTable('ujian', true);
        $this->forge->dropTable('bank_soal', true);
    }
}
