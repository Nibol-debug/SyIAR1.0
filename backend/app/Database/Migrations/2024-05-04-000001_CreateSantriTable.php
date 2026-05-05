<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateSantriTable extends Migration
{
    public function up()
    {
        // Cek apakah tabel sudah ada
        if ($this->db->tableExists('santri')) {
            echo "Tabel santri sudah ada, skip...\n";
            return;
        }
        
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nis' => ['type' => 'VARCHAR', 'constraint' => 20],
            'nisn' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama_panggilan' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'default' => 'L'],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'agama' => ['type' => 'ENUM', 'constraint' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'], 'default' => 'Islam'],
            'anak_ke' => ['type' => 'INT', 'default' => 1],
            'jumlah_saudara' => ['type' => 'INT', 'default' => 0],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'rt' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'rw' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'desa' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kecamatan' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kabupaten' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'provinsi' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kode_pos' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'nama_ayah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_ibu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pekerjaan_ayah' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pekerjaan_ibu' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pendidikan_ayah' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pendidikan_ibu' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nomor_telepon' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'nomor_telepon_ortu' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'email_ortu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kelas' => ['type' => 'VARCHAR', 'constraint' => 20],
            'tahun_masuk' => ['type' => 'YEAR'],
            'tahun_lulus' => ['type' => 'YEAR', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif', 'pindah', 'dropout', 'lulus', 'alumni'], 'default' => 'aktif'],
            'status_alumni' => ['type' => 'ENUM', 'constraint' => ['non_alumni', 'alumni_sma', 'alumni_kuliah', 'alumni_bekerja'], 'default' => 'non_alumni'],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nis');
        $this->forge->addKey(['kelas', 'status']);
        $this->forge->createTable('santri');
    }
    
    public function down()
    {
        $this->forge->dropTable('santri', true);
    }
}
