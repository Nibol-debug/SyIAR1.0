<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpdbRegistrationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode_pendaftaran' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama_calon' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 50],
            'tanggal_lahir' => ['type' => 'DATE'],
            'alamat' => ['type' => 'TEXT'],
            'no_telepon' => ['type' => 'VARCHAR', 'constraint' => 20],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama_ayah' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama_ibu' => ['type' => 'VARCHAR', 'constraint' => 100],
            'pekerjaan_ortu' => ['type' => 'VARCHAR', 'constraint' => 50],
            'asal_sekolah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jenjang_daftar' => ['type' => 'VARCHAR', 'constraint' => 20],
            'jalur_pendaftaran' => ['type' => 'VARCHAR', 'constraint' => 30],
            'status_pendaftaran' => ['type' => 'ENUM', 'constraint' => ['draft', 'submitted', 'verified', 'accepted', 'rejected'], 'default' => 'draft'],
            'catatan_admin' => ['type' => 'TEXT', 'null' => true],
            'file_kk' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'file_akta' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'file_foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['status_pendaftaran', 'jenjang_daftar']);
        $this->forge->createTable('ppdb_registrations', true);
    }

    public function down()
    {
        $this->forge->dropTable('ppdb_registrations', true);
    }
}
