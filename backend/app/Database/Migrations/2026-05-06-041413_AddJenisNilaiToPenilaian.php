<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenisNilaiToPenilaian extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penilaian', [
            'jenis_nilai' => [
                'type'       => 'ENUM',
                'constraint' => ['harian', 'uts', 'uas'],
                'default'    => 'harian',
                'after'      => 'nilai'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penilaian', 'jenis_nilai');
    }
}
