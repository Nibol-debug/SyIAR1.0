<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SantriSeeder extends Seeder
{
    public function run()
    {
        $totalTarget = 1300;

        // Nama depan laki-laki
        $namaDepanL = [
            'Ahmad','Muhammad','Abdullah','Ibrahim','Umar','Ali','Hasan','Husain','Yusuf','Ismail',
            'Dzaki','Faris','Rizqi','Naufal','Hafiz','Zaid','Bilal','Rayhan','Azzam','Alif',
            'Aqil','Bintang','Daffa','Fadhil','Ghani','Hakim','Ihsan','Jibril','Khalid','Lukman',
            'Mikail','Nabil','Omar','Putra','Qadir','Rafi','Syahid','Taqiy','Uwais','Vino',
            'Wahid','Yamin','Zaki','Arif','Bayu','Chairul','Dimas','Eko','Farhan','Gibran',
            'Hamzah','Ikram','Jauhar','Khairi','Labib','Mujahid','Nawfal','Obaid','Prasetya','Qasim',
            'Ridwan','Salman','Taufiq','Ulil','Wafi','Yasir','Zufar','Andi','Bagus','Candra',
            'Danial','Erlangga','Fikri','Galih','Hanif','Idris','Jamal','Kamal','Luqman','Mahdi',
            'Nazir','Oji','Pandu','Rafif','Sabiq','Thoriq','Usman','Wira','Yudha','Zainal',
            'Akbar','Burhan','Dani','Faiz','Haidar','Ilham','Kenzie','Mirza','Rasyid','Syafiq',
        ];

        // Nama depan perempuan
        $namaDepanP = [
            'Aisyah','Fatimah','Khadijah','Maryam','Zainab','Hafshah','Safiyyah','Ruqayyah','Aminah','Sumayyah',
            'Zahra','Nayla','Syifa','Alya','Bilqis','Calista','Dina','Erina','Farah','Ghania',
            'Halimah','Inaya','Jasmin','Kamila','Laila','Maisarah','Nabila','Olfah','Putri','Qonita',
            'Rahma','Salwa','Tsabita','Ulfah','Vina','Warda','Yasmin','Zulaika','Afifah','Balqis',
            'Cantika','Dania','Elma','Fakhira','Ghaida','Hana','Iffah','Jihan','Khansa','Luthfia',
            'Marwah','Nisa','Okta','Puspita','Raisa','Salsabila','Tiara','Ummu','Vanya','Wulan',
            'Yumna','Zara','Aini','Berlian','Citra','Dewi','Fitri','Gina','Husna','Isna',
            'Juliana','Kayla','Latifah','Mutia','Nadya','Olga','Permata','Rania','Shinta','Tari',
            'Uswatun','Vera','Widya','Yuli','Zulfa','Arina','Bunga','Cahya','Dara','Elisa',
            'Firda','Gita','Hamidah','Irma','Jelita','Kartini','Lisa','Mila','Nuha','Sari',
        ];

        // Nama belakang / keluarga
        $namaBelakang = [
            'Pratama','Saputra','Hidayat','Hakim','Ramadhan','Maulana','Fadillah','Firmansyah','Kurniawan','Setiawan',
            'Nugroho','Wicaksono','Putra','Santoso','Wijaya','Utama','Hadi','Syahputra','Gunawan','Permana',
            'Amin','Anwar','Bakri','Darmawan','Effendi','Fauzi','Ghazali','Hamdan','Iskandar','Jazuli',
            'Kamilin','Latif','Mahendra','Nasution','Oktavian','Pradipta','Qureshi','Rahman','Sulaiman','Tanjung',
            'Udin','Virdaus','Wahyudi','Yaqub','Zulkarnain','Harahap','Siregar','Lubis','Daulay','Batubara',
            'Hutapea','Manurung','Siahaan','Panjaitan','Simanjuntak','Sinaga','Aritonang','Situmorang','Nababan','Tampubolon',
            'Al-Farisi','Al-Ghifari','Ar-Rasyid','Al-Habsyi','Al-Amin','Ash-Shiddiq','Al-Faruq','Al-Hasan','Al-Husaini','Al-Bukhari',
            'Abdurrahman','Abdillah','Abdussalam','Abdurrazaq','Abdulkarim','Abdulmalik','Abdulaziz','Abdulwahid','Abdulhadi','Abdulbasit',
        ];

        // Nama ayah
        $namaAyah = [
            'H. Ahmad','Ir. Budi','Drs. Cahyo','H. Dedi','Ust. Eko','H. Farid','Dr. Gunawan','H. Hendra','Ir. Irfan','H. Joko',
            'Ust. Karim','H. Lukman','Drs. Mahmud','H. Nasir','Ir. Omar','H. Purnomo','Ust. Qasim','H. Rahmat','Dr. Surya','H. Taufiq',
            'H. Usman','Ir. Vino','H. Wahyu','Ust. Yusuf','H. Zainal','Drs. Arif','H. Bambang','Ir. Cholid','H. Dimas','Ust. Endang',
        ];

        // Nama ibu
        $namaIbu = [
            'Hj. Aminah','Hj. Budiarti','Hj. Citra','Hj. Dewi','Hj. Endang','Hj. Fatimah','Hj. Gina','Hj. Halimah','Hj. Ira','Hj. Juliana',
            'Hj. Kartini','Hj. Lestari','Hj. Maryam','Hj. Nurhayati','Hj. Oktavia','Hj. Puji','Hj. Qonita','Hj. Rahmawati','Hj. Siti','Hj. Tuti',
            'Hj. Umi','Hj. Vera','Hj. Wati','Hj. Yuni','Hj. Zubaida','Hj. Asih','Hj. Binti','Hj. Cahya','Hj. Darmi','Hj. Evi',
        ];

        // Pekerjaan orang tua
        $pekerjaanOrtu = [
            'Wiraswasta','PNS','Guru','Dosen','Pedagang','Petani','Buruh','TNI/POLRI','Dokter','Insinyur',
            'Pengacara','Nelayan','Karyawan Swasta','Pengusaha','Sopir','Tukang','Penjahit','Montir','Satpam','Ojol',
        ];

        // Kota lahir
        $kotaLahir = [
            'Jakarta','Bandung','Surabaya','Yogyakarta','Semarang','Malang','Medan','Palembang','Makassar','Denpasar',
            'Solo','Bogor','Depok','Tangerang','Bekasi','Cirebon','Tasikmalaya','Sukabumi','Garut','Purwokerto',
            'Pekanbaru','Padang','Lampung','Banjarmasin','Pontianak','Manado','Ambon','Jayapura','Mataram','Kupang',
        ];

        // Alamat prefix
        $jalan = [
            'Jl. Merdeka','Jl. Ahmad Yani','Jl. Sudirman','Jl. Gatot Subroto','Jl. Diponegoro',
            'Jl. Imam Bonjol','Jl. Pahlawan','Jl. Mawar','Jl. Melati','Jl. Kenanga',
            'Jl. Anggrek','Jl. Cempaka','Jl. Dahlia','Jl. Flamboyan','Jl. Kemuning',
            'Jl. Raya Utama','Jl. Pemuda','Jl. Veteran','Jl. KH. Hasyim','Jl. RE. Martadinata',
        ];

        // Get kelas IDs from database
        $kelasRows = $this->db->table('kelas')->where('is_active', 1)->orderBy('id')->get()->getResultArray();
        if (empty($kelasRows)) {
            echo "⚠️  No kelas found. Run Phase3Seeder first!\n";
            return;
        }
        $kelasIds = array_column($kelasRows, 'id');
        $kelasCount = count($kelasIds);

        // Check existing santri count
        $existingCount = $this->db->table('santris')->countAllResults();
        $toInsert = $totalTarget - $existingCount;
        
        if ($toInsert <= 0) {
            echo "✅ Sudah ada {$existingCount} santri. Target {$totalTarget} tercapai.\n";
            return;
        }

        echo "📊 Existing: {$existingCount}, Target: {$totalTarget}, To insert: {$toInsert}\n";

        // Seed santri in batches
        $batchSize = 100;
        $inserted = 0;
        $batch = [];
        $now = date('Y-m-d H:i:s');

        mt_srand(42); // Deterministic seed for reproducibility

        for ($i = 0; $i < $toInsert; $i++) {
            $gender = mt_rand(0, 1) ? 'L' : 'P';
            $namaPool = $gender === 'L' ? $namaDepanL : $namaDepanP;
            
            $firstName  = $namaPool[mt_rand(0, count($namaPool) - 1)];
            $lastName   = $namaBelakang[mt_rand(0, count($namaBelakang) - 1)];
            $fullName   = $firstName . ' ' . $lastName;
            
            // NIS format: SANTRI/YYYY/NNNN
            $nisNum = str_pad($existingCount + $i + 1, 4, '0', STR_PAD_LEFT);
            $nis = 'SANTRI/2025/' . $nisNum;
            
            // NISN: 10 digit random
            $nisn = '00' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            // Random birthdate (age 6-18)
            $age = mt_rand(6, 18);
            $birthYear = 2026 - $age;
            $birthMonth = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
            $birthDay = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
            $birthDate = "{$birthYear}-{$birthMonth}-{$birthDay}";
            
            // Assign to kelas based on index distribution
            $kelasIndex = $i % $kelasCount;
            $kelasId = $kelasIds[$kelasIndex];
            
            // Status: 95% aktif, 3% lulus, 2% pindah
            $statusRand = mt_rand(1, 100);
            $status = $statusRand <= 95 ? 'aktif' : ($statusRand <= 98 ? 'lulus' : 'pindah');
            
            // Entry date
            $entryYear = mt_rand(2020, 2025);
            $entryMonth = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
            $tanggalMasuk = "{$entryYear}-{$entryMonth}-01";
            
            $batch[] = [
                'nis'            => $nis,
                'nisn'           => $nisn,
                'nama_lengkap'   => $fullName,
                'nama_panggilan' => $firstName,
                'jenis_kelamin'  => $gender,
                'tempat_lahir'   => $kotaLahir[mt_rand(0, count($kotaLahir) - 1)],
                'tanggal_lahir'  => $birthDate,
                'alamat'         => $jalan[mt_rand(0, count($jalan) - 1)] . ' No. ' . mt_rand(1, 150) . ', RT ' . str_pad(mt_rand(1, 20), 2, '0', STR_PAD_LEFT) . '/RW ' . str_pad(mt_rand(1, 10), 2, '0', STR_PAD_LEFT),
                'no_telepon'     => '08' . mt_rand(11, 99) . mt_rand(1000000, 9999999),
                'email'          => strtolower(str_replace(' ', '.', $fullName)) . mt_rand(1, 99) . '@santri.syiar.id',
                'nama_ayah'      => $namaAyah[mt_rand(0, count($namaAyah) - 1)] . ' ' . $namaBelakang[mt_rand(0, count($namaBelakang) - 1)],
                'nama_ibu'       => $namaIbu[mt_rand(0, count($namaIbu) - 1)] . ' ' . $namaBelakang[mt_rand(0, count($namaBelakang) - 1)],
                'no_hp_ortu'     => '08' . mt_rand(11, 99) . mt_rand(1000000, 9999999),
                'pekerjaan_ortu' => $pekerjaanOrtu[mt_rand(0, count($pekerjaanOrtu) - 1)],
                'kelas_id'       => $kelasId,
                'status'         => $status,
                'tanggal_masuk'  => $tanggalMasuk,
                'tanggal_keluar' => $status !== 'aktif' ? date('Y-m-d') : null,
                'foto'           => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
            
            // Insert in batches
            if (count($batch) >= $batchSize) {
                $this->db->table('santris')->insertBatch($batch);
                $inserted += count($batch);
                echo "   ⏳ Inserted: {$inserted}/{$toInsert}\n";
                $batch = [];
            }
        }
        
        // Insert remaining
        if (!empty($batch)) {
            $this->db->table('santris')->insertBatch($batch);
            $inserted += count($batch);
        }
        
        $finalCount = $this->db->table('santris')->countAllResults();
        echo "✅ Seeded: {$inserted} Santri (Total in DB: {$finalCount})\n";
    }
}
