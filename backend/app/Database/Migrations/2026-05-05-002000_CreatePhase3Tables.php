<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase3Tables extends Migration
{
    public function up()
    {
        // ============ TAHUN AJARAN ============
        if (!$this->db->tableExists('tahun_ajaran')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode'             => ['type' => 'VARCHAR', 'constraint' => 9, 'comment' => '2025/2026'],
                'nama'             => ['type' => 'VARCHAR', 'constraint' => 50],
                'semester'         => ['type' => 'ENUM', 'constraint' => ['ganjil', 'genap'], 'default' => 'ganjil'],
                'tanggal_mulai'    => ['type' => 'DATE'],
                'tanggal_selesai'  => ['type' => 'DATE'],
                'is_active'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
                'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('kode');
            $this->forge->createTable('tahun_ajaran');
            echo "  ✅ Table: tahun_ajaran\n";
        }

        // ============ JADWAL PELAJARAN ============
        if (!$this->db->tableExists('jadwal_pelajaran')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kelas_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'mapel_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'guru_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'comment' => 'pegawai.id'],
                'hari'        => ['type' => 'ENUM', 'constraint' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']],
                'jam_mulai'   => ['type' => 'TIME'],
                'jam_selesai' => ['type' => 'TIME'],
                'ruangan'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['kelas_id', 'hari']);
            $this->forge->createTable('jadwal_pelajaran');
            echo "  ✅ Table: jadwal_pelajaran\n";
        }

        // ============ PRESENSI SISWA ============
        if (!$this->db->tableExists('presensi_siswa')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'santri_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kelas_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'mapel_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'tanggal'       => ['type' => 'DATE'],
                'status'        => ['type' => 'ENUM', 'constraint' => ['H', 'I', 'S', 'A'], 'default' => 'H'],
                'keterangan'    => ['type' => 'TEXT', 'null' => true],
                'pencatat_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['santri_id', 'tanggal']);
            $this->forge->addKey('kelas_id');
            $this->forge->createTable('presensi_siswa');
            echo "  ✅ Table: presensi_siswa\n";
        }

        // ============ JURNAL MENGAJAR ============
        if (!$this->db->tableExists('jurnal_mengajar')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'guru_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'comment' => 'pegawai.id'],
                'kelas_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'mapel_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tanggal'     => ['type' => 'DATE'],
                'jam_ke'      => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'materi'      => ['type' => 'TEXT'],
                'tugas'       => ['type' => 'TEXT', 'null' => true],
                'keterangan'  => ['type' => 'TEXT', 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['guru_id', 'tanggal']);
            $this->forge->createTable('jurnal_mengajar');
            echo "  ✅ Table: jurnal_mengajar\n";
        }

        // ============ KALENDER AKADEMIK ============
        if (!$this->db->tableExists('kalender_akademik')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'judul'            => ['type' => 'VARCHAR', 'constraint' => 200],
                'tanggal_mulai'    => ['type' => 'DATE'],
                'tanggal_selesai'  => ['type' => 'DATE', 'null' => true],
                'jenis'            => ['type' => 'ENUM', 'constraint' => ['ujian', 'libur', 'kegiatan', 'rapat', 'lainnya'], 'default' => 'kegiatan'],
                'deskripsi'        => ['type' => 'TEXT', 'null' => true],
                'warna'            => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#3b82f6'],
                'is_active'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
                'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('kalender_akademik');
            echo "  ✅ Table: kalender_akademik\n";
        }
    }

    public function down()
    {
        $this->forge->dropTable('kalender_akademik', true);
        $this->forge->dropTable('jurnal_mengajar', true);
        $this->forge->dropTable('presensi_siswa', true);
        $this->forge->dropTable('jadwal_pelajaran', true);
        $this->forge->dropTable('tahun_ajaran', true);
    }
}
