<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateAlumniTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('alumni')) {
            echo "Tabel alumni sudah ada, skip...\n";
            return;
        }
        
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'santri_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tahun_lulus' => ['type' => 'YEAR'],
            'nomor_ijazah' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nomor_skhun' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status_after' => ['type' => 'ENUM', 'constraint' => ['kuliah', 'bekerja', 'wirausaha', 'mencari_kerja', 'lainnya'], 'default' => 'kuliah'],
            'nama_perguruan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jurusan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_perusahaan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jabatan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alamat_terkini' => ['type' => 'TEXT', 'null' => true],
            'kontak_terkini' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'email_terkini' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'instagram' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'facebook' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kesan_pesan' => ['type' => 'TEXT', 'null' => true],
            'partisipasi_reuni' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('santri_id', 'santri', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('alumni');
    }
    
    public function down()
    {
        $this->forge->dropTable('alumni', true);
    }
}
