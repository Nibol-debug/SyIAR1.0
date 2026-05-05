<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSantrisTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nis' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nisn' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama_panggilan' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'default' => 'L'],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'no_telepon' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_ayah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_ibu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_hp_ortu' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'pekerjaan_ortu' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif', 'lulus', 'pindah', 'keluar'], 'default' => 'aktif'],
            'tanggal_masuk' => ['type' => 'DATE', 'null' => true],
            'tanggal_keluar' => ['type' => 'DATE', 'null' => true],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['nis', 'nisn']);
        $this->forge->addKey(['status', 'kelas_id']);
        $this->forge->createTable('santris', true);
    }

    public function down()
    {
        $this->forge->dropTable('santris', true);
    }
}
