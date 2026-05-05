<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kelas' => ['type' => 'VARCHAR', 'constraint' => 50],
            'tingkat' => ['type' => 'VARCHAR', 'constraint' => 20],
            'jurusan' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tahun_ajaran' => ['type' => 'VARCHAR', 'constraint' => 9],
            'wali_kelas_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'kapasitas' => ['type' => 'INT', 'constraint' => 3, 'default' => 30],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['tingkat', 'tahun_ajaran']);
        $this->forge->createTable('kelas', true);
    }

    public function down()
    {
        $this->forge->dropTable('kelas', true);
    }
}
