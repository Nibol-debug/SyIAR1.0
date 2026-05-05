<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase2Tables extends Migration
{
    public function up()
    {
        // ============ PEGAWAI (Staff/Teachers) ============
        if (!$this->db->tableExists('pegawai')) {
            $this->forge->addField([
                'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'nip'                 => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'nuptk'               => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'nama_lengkap'        => ['type' => 'VARCHAR', 'constraint' => 100],
                'jenis_kelamin'       => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
                'tempat_lahir'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'tanggal_lahir'       => ['type' => 'DATE', 'null' => true],
                'alamat'              => ['type' => 'TEXT', 'null' => true],
                'no_telepon'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'email'               => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'pendidikan_terakhir' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'comment' => 'SMA/D3/S1/S2/S3'],
                'gelar'               => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'universitas'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tahun_lulus'         => ['type' => 'YEAR', 'null' => true],
                'status_kepegawaian'  => ['type' => 'ENUM', 'constraint' => ['GTT', 'PTT', 'Tetap', 'Honorer'], 'default' => 'GTT'],
                'jabatan'             => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'tanggal_bergabung'   => ['type' => 'DATE', 'null' => true],
                'foto'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'is_active'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'          => ['type' => 'DATETIME', 'null' => true],
                'updated_at'          => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('pegawai');
            echo "  ✅ Table: pegawai\n";
        }

        // ============ PEGAWAI DOKUMEN ============
        if (!$this->db->tableExists('pegawai_dokumen')) {
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'pegawai_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'jenis_dokumen'  => ['type' => 'VARCHAR', 'constraint' => 50, 'comment' => 'ijazah/sertifikasi/sk/ktp'],
                'nama_file'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'file_path'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'keterangan'     => ['type' => 'TEXT', 'null' => true],
                'uploaded_at'    => ['type' => 'DATETIME', 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
                'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('pegawai_id');
            $this->forge->createTable('pegawai_dokumen');
            echo "  ✅ Table: pegawai_dokumen\n";
        }

        // ============ PRESENSI PEGAWAI ============
        if (!$this->db->tableExists('presensi_pegawai')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'pegawai_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tanggal'     => ['type' => 'DATE'],
                'jam_masuk'   => ['type' => 'TIME', 'null' => true],
                'jam_keluar'  => ['type' => 'TIME', 'null' => true],
                'status'      => ['type' => 'ENUM', 'constraint' => ['hadir', 'izin', 'sakit', 'alpha', 'cuti'], 'default' => 'hadir'],
                'keterangan'  => ['type' => 'TEXT', 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('pegawai_id');
            $this->forge->addUniqueKey(['pegawai_id', 'tanggal'], 'unique_presensi_pegawai');
            $this->forge->createTable('presensi_pegawai');
            echo "  ✅ Table: presensi_pegawai\n";
        }

        // ============ MATA PELAJARAN ============
        if (!$this->db->tableExists('mata_pelajaran')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_mapel'  => ['type' => 'VARCHAR', 'constraint' => 20],
                'nama_mapel'  => ['type' => 'VARCHAR', 'constraint' => 100],
                'kelompok'    => ['type' => 'ENUM', 'constraint' => ['wajib', 'peminatan', 'muatan_lokal', 'keagamaan'], 'default' => 'wajib'],
                'deskripsi'   => ['type' => 'TEXT', 'null' => true],
                'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('kode_mapel');
            $this->forge->createTable('mata_pelajaran');
            echo "  ✅ Table: mata_pelajaran\n";
        }

        // ============ PEGAWAI MAPEL (Guru ↔ Mapel) ============
        if (!$this->db->tableExists('pegawai_mapel')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'pegawai_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'mapel_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['pegawai_id', 'mapel_id']);
            $this->forge->createTable('pegawai_mapel');
            echo "  ✅ Table: pegawai_mapel\n";
        }
    }

    public function down()
    {
        $this->forge->dropTable('pegawai_mapel', true);
        $this->forge->dropTable('presensi_pegawai', true);
        $this->forge->dropTable('pegawai_dokumen', true);
        $this->forge->dropTable('mata_pelajaran', true);
        $this->forge->dropTable('pegawai', true);
    }
}
