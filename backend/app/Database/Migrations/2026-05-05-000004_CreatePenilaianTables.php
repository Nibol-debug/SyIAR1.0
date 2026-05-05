<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianTables extends Migration
{
    public function up()
    {
        // 1. Tabel kategori_penilaian
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kategori' => ['type' => 'VARCHAR', 'constraint' => 50],
            'kode_kategori' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'warna' => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#3b82f6'],
            'urutan' => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('kategori_penilaian', true);

        // 2. Tabel aspek_penilaian - FK inline dalam field definition
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kategori_id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true,
                'comment' => 'FK to kategori_penilaian.id'
            ],
            'nama_aspek' => ['type' => 'VARCHAR', 'constraint' => 100],
            'kode_aspek' => ['type' => 'VARCHAR', 'constraint' => 30],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'skala_min' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'skala_max' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 100],
            'bobot' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 1.00],
            'tipe_input' => ['type' => 'ENUM', 'constraint' => ['number', 'range', 'select', 'text'], 'default' => 'number'],
            'options' => ['type' => 'TEXT', 'null' => true],
            'urutan' => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('kategori_id');
        $this->forge->createTable('aspek_penilaian', true);
        
        // Add FK using raw SQL (CI4 Forge limitation workaround)
        $this->db->query("ALTER TABLE aspek_penilaian 
            ADD CONSTRAINT fk_aspek_kategori 
            FOREIGN KEY (kategori_id) REFERENCES kategori_penilaian(id) 
            ON DELETE CASCADE ON UPDATE CASCADE");

        // 3. Tabel penilaian
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'santri_id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true,
                'comment' => 'FK to santris.id'
            ],
            'aspek_id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true,
                'comment' => 'FK to aspek_penilaian.id'
            ],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nilai' => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'tanggal_penilaian' => ['type' => 'DATE'],
            'periode' => ['type' => 'VARCHAR', 'constraint' => 20],
            'is_draft' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['santri_id', 'periode']);
        $this->forge->createTable('penilaian', true);
        
        // Add FKs using raw SQL
        $this->db->query("ALTER TABLE penilaian 
            ADD CONSTRAINT fk_penilaian_santri 
            FOREIGN KEY (santri_id) REFERENCES santris(id) 
            ON DELETE CASCADE ON UPDATE CASCADE");
            
        $this->db->query("ALTER TABLE penilaian 
            ADD CONSTRAINT fk_penilaian_aspek 
            FOREIGN KEY (aspek_id) REFERENCES aspek_penilaian(id) 
            ON DELETE CASCADE ON UPDATE CASCADE");
            
        // Optional: guru_id FK (skip if users table structure uncertain)
        // $this->db->query("ALTER TABLE penilaian ADD CONSTRAINT fk_penilaian_guru FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE SET NULL");
    }

    public function down()
    {
        $this->forge->dropTable('penilaian', true);
        $this->forge->dropTable('aspek_penilaian', true);
        $this->forge->dropTable('kategori_penilaian', true);
    }
}
