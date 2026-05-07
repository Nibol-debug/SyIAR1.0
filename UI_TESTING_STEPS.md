# 🧪 UI TESTING STEPS — BROWSER & POSTMAN

## TESTING VIA BROWSER

### ✅ LOGIN & DASHBOARD
1. Buka http://localhost:3000
2. Login dengan:
   - Username: `superadmin`
   - Password: `Admin123!`
3. ✅ Seharusnya redirect ke dashboard
4. Lihat statistik: Total Santri (1.300), Guru, Kelas (24)
5. Lihat navigation menu di sidebar

---

## 🧑‍💼 PHASE 2 - PEGAWAI / HR SYSTEM

### 1. View Pegawai List
1. Menu → Kepegawaian → Data Pegawai
2. ✅ Lihat daftar 20 pegawai (yang sudah di-seed)
3. Klik "Filter" dan test:
   - Search by name: ketik "Ahmad"
   - Filter by status: pilih "GTT"
   - Klik filter
4. ✅ Hasil filter muncul

### 2. Create Pegawai Baru
1. Pegawai list → Klik tombol "Tambah Pegawai"
2. Isi form:
   - Nama Lengkap: `Ibu Siti Nurhaliza`
   - Jenis Kelamin: `Perempuan`
   - Pendidikan: `S1`
   - Status: `GTT`
   - Jabatan: `Guru BK`
3. Klik "Simpan"
4. ✅ Success message muncul
5. Kembali ke list, lihat data baru

### 3. Edit Pegawai
1. Pegawai list → Klik "Edit" di salah satu row
2. Update data: Status → `Tetap`
3. Klik "Simpan"
4. ✅ Data terupdate

### 4. View Detail Pegawai
1. Pegawai list → Klik nama pegawai
2. ✅ Lihat detail: nama, status, jabatan, mata pelajaran

### 5. Presensi Pegawai
1. Menu → Kepegawaian → Presensi Pegawai
2. Pilih tanggal hari ini
3. Isi status untuk setiap pegawai:
   - H (Hadir)
   - I (Izin)
   - S (Sakit)
   - A (Alpa)
4. Klik "Simpan"
5. ✅ Presensi tersimpan
6. Klik "Lihat Rekap" → lihat laporan presensi bulanan

---

## 📚 PHASE 3 - AKADEMIK

### 1. View Kelas
1. Menu → Manajemen → Data Kelas
2. ✅ Lihat 24 kelas yang sudah di-seed
3. Cek kolom: Nama Kelas, Jurusan, Wali Kelas

### 2. View Santri
1. Menu → Manajemen → Data Santri
2. ✅ Lihat daftar santri
3. Test filter:
   - Klik dropdown kelas
   - Pilih satu kelas
   - Lihat santri di kelas tersebut
4. Search: ketik NIS atau nama
5. ✅ Hasil pencarian muncul

### 3. Input Penilaian
1. Menu → Akademik → Input Nilai
2. Pilih:
   - Kategori Penilaian (Harian, UTS, UAS)
   - Kelas
   - Santri
3. ✅ Muncul form input nilai per aspek
4. Masukkan nilai untuk setiap aspek (1-100)
5. Klik "Simpan"
6. ✅ Nilai tersimpan

### 4. Lihat Rekap Penilaian
1. Menu → Akademik → Rekap Nilai
2. Pilih Kelas dan Periode (Bulan/Tahun)
3. ✅ Lihat tabel nilai semua santri
4. Klik "Export Excel"
5. ✅ File Excel download

### 5. Input Presensi Siswa
1. Menu → Akademik → Presensi Siswa
2. Pilih:
   - Kelas
   - Tanggal
3. ✅ Lihat daftar santri dengan input status
4. Pilih status untuk setiap santri
5. Klik "Simpan"
6. ✅ Presensi tersimpan
7. Klik "Rekap Presensi" untuk lihat laporan

---

## 🎓 PHASE 4 - CBT / UJIAN ONLINE

### 1. View Bank Soal
1. Menu → CBT → Bank Soal
2. ✅ Lihat daftar 10+ soal
3. Test filter:
   - Mata Pelajaran filter
   - Tingkat Kesulitan (Mudah, Sedang, Sulit)
   - Search soal
4. ✅ Filter bekerja

### 2. Create Soal Baru
1. Bank Soal → Klik "Buat Soal Baru"
2. Isi form:
   - Mata Pelajaran: `Matematika`
   - Tipe Soal: `PG (Pilihan Ganda)` / `Esai`
   - Pertanyaan: `Berapa hasil 2+2?`
   - Pilihan Jawaban (jika PG): `3, 4, 5, 6`
   - Kunci Jawaban: `4`
   - Tingkat: `Mudah`
3. Klik "Simpan"
4. ✅ Soal tersimpan
5. Lihat di daftar

### 3. Edit Soal
1. Bank Soal → Klik "Edit" di salah satu soal
2. Update pertanyaan
3. Klik "Simpan"
4. ✅ Soal terupdate

### 4. Manajemen Ujian
1. Menu → CBT → Manajemen Ujian
2. ✅ Lihat ujian yang sudah ada (1 ujian seeded)
3. Lihat status: `Aktif` atau `Selesai`
4. Lihat token akses

### 5. Create Ujian Baru
1. Manajemen Ujian → Klik "Buat Jadwal Ujian"
2. Isi form:
   - Judul: `UTS Bahasa Arab Kelas 9`
   - Mata Pelajaran: `Bahasa Arab`
   - Kelas: Pilih multiple (9A, 9B, 9C)
   - Durasi: `45` menit
   - Jumlah Soal: `5`
   - Tanggal Mulai: `2026-05-10`
   - Shuffle Soal: ✓ check
   - Shuffle Jawaban: ✓ check
3. Klik tab "Pilih Soal"
4. ✅ Lihat bank soal
5. Check 5 soal
6. Klik "Simpan Ujian"
7. ✅ Ujian tersimpan

### 6. Generate Token & Aktivasi
1. Manajemen Ujian → Lihat ujian yang dibuat
2. Klik "Aktifkan & Token"
3. ✅ Popup: Token akses muncul (misal: `ABC123DEF456`)
4. Catat token ini untuk digunakan siswa
5. Sekarang status berubah menjadi `AKTIF`

### 7. Monitor Ujian (Real-time)
1. Ujian aktif → Klik "Monitor"
2. ✅ Lihat panel monitoring:
   - Daftar siswa yang sedang ujian
   - Progress ujian setiap siswa
   - Status jawaban

---

## 🔐 ROLE & PERMISSION MANAGEMENT

### 1. View Roles
1. Menu → Settings → Role Management
2. ✅ Lihat 4 roles:
   - Super Admin (32 permissions)
   - Admin Akademik
   - Guru
   - Kepala Bagian

### 2. View Permissions
1. Role Management → Tab "Permissions"
2. ✅ Lihat semua permissions:
   - user.* (create, read, update, delete)
   - pegawai.* (create, read, update, delete)
   - penilaian.* (create, read, update, delete, export)
   - dst

### 3. Assign Permission ke Role
1. Role Management → Klik role "Admin Akademik"
2. ✅ Lihat permission list
3. Check beberapa permissions:
   - penilaian.create
   - penilaian.read
   - penilaian.update
   - santri.read
4. Klik "Simpan"
5. ✅ Permissions tersimpan

---

## 🧪 POSTMAN TESTING

### Setup di Postman

#### 1. Create Environment
1. Buka Postman
2. Klik "Environments" → "+ Create New"
3. Nama: `SyIAR Local`
4. Tambah variables:
   ```
   base_url: http://127.0.0.1:8081/api
   token: (akan diisi dari login response)
   ```
5. Save

#### 2. Create Collection
1. "+ Create New" → Collection
2. Nama: `SyIAR Gemilang API`
3. Pilih environment: `SyIAR Local`

#### 3. Create Login Request
1. Collection → + Add Request
2. Nama: `1. Login`
3. Method: `POST`
4. URL: `{{base_url}}/auth/login`
5. Body → raw → JSON:
```json
{
  "username": "superadmin",
  "password": "Admin123!"
}
```
6. Save
7. Click "Send"
8. ✅ Response: token ditampilkan

#### 4. Save Token ke Environment
1. Response → Tests (tab)
2. Tambahkan script:
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
}
```
3. Save
4. Click "Send" lagi
5. Token otomatis tersimpan di environment

#### 5. Create Requests Lain
Gunakan template:
```
Method: GET
URL: {{base_url}}/pegawai
Headers:
  Authorization: Bearer {{token}}
```

---

## 📊 EXPECTED TEST RESULTS

### ✅ All Passing
```
✓ Server health check
✓ Login successful (JWT token generated)
✓ Get profile (me endpoint)
✓ Pegawai CRUD (create, read, update, delete)
✓ Mata Pelajaran list
✓ Presensi input & recap
✓ Kelas list & detail
✓ Santri list & search
✓ Penilaian input & recap
✓ Presensi siswa
✓ Bank soal CRUD
✓ Ujian CRUD & token generation
✓ Dashboard statistics
```

---

## 🐛 COMMON ISSUES & SOLUTIONS

| Issue | Cause | Solution |
|-------|-------|----------|
| 401 Unauthorized | Token expired/invalid | Login ulang, copy token baru |
| CORS Error | Domain tidak di-whitelist | Pastikan frontend port 3000 |
| 404 Not Found | Endpoint salah | Cek kembali URL endpoint |
| 500 Server Error | Database connection | Cek database running |
| Port already in use | Server sudah berjalan | Kill process: `lsof -i :8081` |

---

## 📋 REGRESSION TESTING CHECKLIST

- [ ] Auth tidak rusak
- [ ] Semua endpoint return 200/201/204
- [ ] Data validation bekerja
- [ ] CORS configured correct
- [ ] Database query efficient
- [ ] Pagination working
- [ ] Search/filter working
- [ ] Create/Update/Delete atomicity
- [ ] Error messages clear
- [ ] Performance acceptable (<1s)

---

**Testing Environments:**
- Development: http://localhost:3000
- Backend API: http://127.0.0.1:8081
- Database: MySQL (localhost)

**Test Data:**
- 1.300 Santri
- 20 Pegawai
- 24 Kelas
- 16 Mata Pelajaran
- 4 Kategori Penilaian
- 10+ Soal Bank
- 1 Ujian Active
