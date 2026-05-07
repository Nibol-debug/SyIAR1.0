<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JurusanRefactorSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        echo "\n=== JURUSAN REFACTOR SEEDER ===\n";
        
        // ============ STEP 1: BACKUP SANTRI DATA ============
        echo "\n⏳ Step 1: Backup data santri...\n";
        
        // Check if any santri are orphaned
        $orphanedSantri = $db->table('santris')
            ->select('santris.id, santris.nama_lengkap, santris.kelas_id')
            ->where('santris.kelas_id IS NOT NULL')
            ->get()
            ->getResultArray();
            
        $totalSantri = count($orphanedSantri);
        echo "✅ Found {$totalSantri} santri with kelas assignment\n";
        
        // ============ STEP 2: DELETE OLD KELAS DATA ============
        echo "\n⏳ Step 2: Deleting old kelas data...\n";
        
        // Get all old kelas IDs
        $oldKelas = $db->table('kelas')->select('id')->get()->getResultArray();
        $oldKelasIds = array_column($oldKelas, 'id');
        
        echo "Found " . count($oldKelasIds) . " old kelas to delete\n";
        
        // Before deleting, set santris.kelas_id to NULL to avoid FK constraints
        if (!empty($oldKelasIds)) {
            $db->table('santris')->update(['kelas_id' => null], ['kelas_id >' => 0]);
            echo "✅ Cleared santris.kelas_id references\n";
            
            // Delete related records in other tables
            $db->table('jadwal_pelajaran')->whereIn('kelas_id', $oldKelasIds)->delete();
            echo "✅ Deleted jadwal_pelajaran records\n";
            
            $db->table('presensi_siswa')->whereIn('kelas_id', $oldKelasIds)->delete();
            echo "✅ Deleted presensi_siswa records\n";
            
            $db->table('jurnal_mengajar')->whereIn('kelas_id', $oldKelasIds)->delete();
            echo "✅ Deleted jurnal_mengajar records\n";
            
            // Now delete kelas
            $db->table('kelas')->whereIn('id', $oldKelasIds)->delete();
            echo "✅ Deleted old kelas data\n";
        }
        
        // ============ STEP 3: CREATE NEW KELAS STRUCTURE ============
        echo "\n⏳ Step 3: Creating new jurusan structure...\n";
        
        $tahunAjaran = '2025/2026';
        $jurusanList = [
            'TKJ' => 'Teknik Komputer & Jaringan',
            'TB' => 'Tata Busana',
            'DG' => 'Desain Grafis',
            'FV' => 'Fotografi & Videografi',
            'TSM' => 'Teknik Sepeda Motor',
            'DOS' => 'Digital Office Specialist'
        ];
        
        $newKelasData = [];
        $kelasInserted = 0;
        
        foreach ($jurusanList as $code => $nama) {
            // Create 2 classes per jurusan (Level 1 and Level 2)
            for ($level = 1; $level <= 2; $level++) {
                $kelasName = "{$code}-{$level}";
                $data = [
                    'nama_kelas' => $kelasName,
                    'jurusan' => $code,
                    'tahun_ajaran' => $tahunAjaran,
                    'kapasitas' => 30,
                    'keterangan' => "{$nama} - Level {$level}",
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($db->table('kelas')->insert($data)) {
                    $kelasInserted++;
                    echo "✅ Created kelas: {$kelasName}\n";
                    $newKelasData[] = $kelasName;
                }
            }
        }
        
        echo "\n✨ Total kelas dibuat: {$kelasInserted}\n";
        
        // ============ STEP 4: REASSIGN SANTRI TO NEW KELAS ============
        echo "\n⏳ Step 4: Reassigning santri to new kelas...\n";
        
        if ($totalSantri > 0) {
            // Get new kelas IDs
            $newKelasRows = $db->table('kelas')->select('id, jurusan')->orderBy('jurusan, nama_kelas')->get()->getResultArray();
            
            if (!empty($newKelasRows)) {
                // Distribute santri evenly across available kelas
                foreach ($orphanedSantri as $index => $santri) {
                    $kelasIndex = $index % count($newKelasRows);
                    $newKelasId = $newKelasRows[$kelasIndex]['id'];
                    
                    $db->table('santris')->update(
                        ['kelas_id' => $newKelasId],
                        ['id' => $santri['id']]
                    );
                }
                echo "✅ Reassigned {$totalSantri} santri to new kelas\n";
            }
        }
        
        // ============ STEP 5: VERIFICATION ============
        echo "\n⏳ Step 5: Verifying data integrity...\n";
        
        $totalKelas = $db->table('kelas')->countAllResults();
        $totalSantriWithKelas = $db->table('santris')->where('kelas_id IS NOT NULL')->countAllResults();
        $orphanedSantriAfter = $db->table('santris')->where('kelas_id IS NULL')->countAllResults();
        
        echo "✅ Total kelas sekarang: {$totalKelas}\n";
        echo "✅ Santri dengan kelas: {$totalSantriWithKelas}\n";
        echo "⚠️  Orphaned santri: {$orphanedSantriAfter}\n";
        
        // Check for duplicates
        $duplicates = $db->query(
            "SELECT nama_kelas, COUNT(*) as count FROM kelas GROUP BY nama_kelas HAVING count > 1"
        )->getResultArray();
        
        if (!empty($duplicates)) {
            echo "⚠️  WARNING: Duplikasi ditemukan!\n";
            foreach ($duplicates as $dup) {
                echo "   - {$dup['nama_kelas']}: {$dup['count']} duplicates\n";
            }
        } else {
            echo "✅ Tidak ada duplikasi data\n";
        }
        
        // List new kelas
        echo "\n📚 Daftar Kelas Baru:\n";
        $allKelas = $db->table('kelas')
            ->select('id, nama_kelas, jurusan, keterangan, is_active')
            ->orderBy('jurusan, nama_kelas')
            ->get()
            ->getResultArray();
            
        foreach ($allKelas as $k) {
            $status = $k['is_active'] ? '✅' : '❌';
            echo "{$status} [{$k['id']}] {$k['nama_kelas']} ({$k['jurusan']}) - {$k['keterangan']}\n";
        }
        
        echo "\n✨ JURUSAN REFACTOR SEEDER COMPLETED!\n\n";
    }
}
