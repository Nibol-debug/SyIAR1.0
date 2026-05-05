<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Rename 'tingkat' column to drop it since 'jurusan' replaces it as the primary identifier.
 * This migration removes the redundant 'tingkat' column from kelas table.
 */
class AlterKelasDropTingkat extends Migration
{
    public function up()
    {
        // Check if tingkat column exists before dropping
        if ($this->db->fieldExists('tingkat', 'kelas')) {
            // First, copy any tingkat values to jurusan where jurusan is null
            $this->db->query("UPDATE kelas SET jurusan = tingkat WHERE jurusan IS NULL OR jurusan = ''");
            
            // Drop the tingkat column
            $this->forge->dropColumn('kelas', 'tingkat');
        }
    }

    public function down()
    {
        // Re-add tingkat column if needed
        $this->forge->addColumn('kelas', [
            'tingkat' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'nama_kelas'],
        ]);
    }
}
