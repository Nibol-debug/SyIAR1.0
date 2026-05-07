<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAspekIdToMapel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('mata_pelajaran', [
            'aspek_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ]
        ]);
        
        $this->db->query("ALTER TABLE mata_pelajaran ADD CONSTRAINT fk_mapel_aspek FOREIGN KEY (aspek_id) REFERENCES aspek_penilaian(id) ON DELETE SET NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE mata_pelajaran DROP FOREIGN KEY fk_mapel_aspek");
        $this->forge->dropColumn('mata_pelajaran', 'aspek_id');
    }
}
