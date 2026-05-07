<?php
require 'vendor/autoload.php';
$config = new Config\Database();
$db = $config->connect();

echo "=== KELAS DATA VERIFICATION ===\n\n";

// Check total kelas
$total = $db->table('kelas')->countAllResults();
echo "Total kelas: $total\n";

// Check kelas by jurusan
$kelas = $db->table('kelas')
    ->select('id, nama_kelas, jurusan, keterangan, is_active')
    ->orderBy('jurusan, nama_kelas')
    ->get()
    ->getResultArray();

echo "\nDaftar Kelas:\n";
foreach ($kelas as $k) {
    $status = $k['is_active'] ? '✅' : '❌';
    echo "$status [{$k['id']}] {$k['nama_kelas']} ({$k['jurusan']}) - {$k['keterangan']}\n";
}

// Check santri distribution
$santriPerKelas = $db->table('santris')
    ->select('kelas_id, COUNT(*) as count')
    ->groupBy('kelas_id')
    ->get()
    ->getResultArray();

echo "\n\nSantri Distribution:\n";
$totalSantri = 0;
foreach ($santriPerKelas as $s) {
    if ($s['kelas_id']) {
        $kelasName = $db->table('kelas')->select('nama_kelas')->where('id', $s['kelas_id'])->get()->getRow();
        echo "- {$kelasName->nama_kelas}: {$s['count']} santri\n";
        $totalSantri += $s['count'];
    }
}
echo "\nTotal santri dengan kelas: $totalSantri\n";

// Check for orphaned records
$orphaned = $db->table('santris')->where('kelas_id IS NULL')->countAllResults();
echo "Orphaned santri (no kelas): $orphaned\n";

// Check for data issues
$duplicates = $db->query(
    "SELECT nama_kelas, COUNT(*) as count FROM kelas GROUP BY nama_kelas HAVING count > 1"
)->getResultArray();

if (!empty($duplicates)) {
    echo "\n⚠️  DUPLICATES FOUND:\n";
    foreach ($duplicates as $dup) {
        echo "- {$dup['nama_kelas']}: {$dup['count']} duplicates\n";
    }
} else {
    echo "\n✅ No duplicates found\n";
}

echo "\n=== END VERIFICATION ===\n";
