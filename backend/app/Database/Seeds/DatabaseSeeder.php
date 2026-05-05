<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "\n╔════════════════════════════════════════╗\n";
        echo "║   🌱 SyIAR Gemilang Database Seeder   ║\n";
        echo "╚════════════════════════════════════════╝\n\n";

        echo "▶ Phase 1: Initial Data (Roles, Permissions, Users)...\n";
        $this->call('InitialDataSeeder');
        echo "\n";
        
        echo "▶ Phase 2: HRM (Pegawai, Mapel, Tahun Ajaran)...\n";
        $this->call('Phase2Seeder');
        echo "\n";
        
        echo "▶ Phase 3: Kelas, Kategori, Aspek, Permissions...\n";
        $this->call('Phase3Seeder');
        echo "\n";
        
        echo "▶ Santri: Seeding 1300 Santri...\n";
        $this->call('SantriSeeder');
        echo "\n";
        
        echo "╔════════════════════════════════════════╗\n";
        echo "║   ✅ All seeds completed successfully! ║\n";
        echo "╚════════════════════════════════════════╝\n\n";
    }
}
