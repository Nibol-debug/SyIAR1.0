# 🧪 PANDUAN TESTING — SYIAR GEMILANG
## Cara Menguji Fitur yang Sudah Ditambahkan

---

## 🚀 STEP 1: PERSIAPAN & MENJALANKAN SERVERS

### Buka Terminal 1: Start Backend API
```bash
cd backend
php spark serve --host 127.0.0.1 --port 8081
```
✅ Harusnya muncul: `CodeIgniter development server started on http://127.0.0.1:8081`

### Buka Terminal 2: Start Frontend
```bash
cd frontend
npm start
```
✅ Harusnya muncul: `Server running on http://localhost:3000`

### Cek Health Check (Terminal 3)
```bash
curl http://127.0.0.1:8081/health
```
✅ Response: 
```json
{
    "status": "ok",
    "timestamp": "2026-05-06 16:40:00",
    "app": "SyIAR Gemilang API"
}
```

---

## 🔐 STEP 2: TESTING AUTHENTICATION (Phase 1)

### A. Login via API
```bash
curl -X POST http://127.0.0.1:8081/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"superadmin","password":"Admin123!"}'
```

✅ **Response Berhasil:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": "1",
            "username": "superadmin",
            "nama_lengkap": "Administrator Utama",
            "role": "super_admin",
            "permissions": [...]
        }
    }
}
```

**Simpan TOKEN untuk testing lanjutan:**
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

### B. Cek Profile User (Menggunakan Token)
```bash
curl -X GET http://127.0.0.1:8081/api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

### C. Login via Browser
1. Buka: http://localhost:3000/
2. Login dengan:
   - Username: `superadmin`
   - Password: `Admin123!`
3. ✅ Harusnya redirect ke dashboard

---

## 👥 STEP 3: TESTING ROLE & PERMISSION (Phase 1-2)

### A. Lihat Semua Roles
```bash
curl -X GET http://127.0.0.1:8081/api/roles \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

✅ Response:
```json
{
    "success": true,
    "data": [
        {"id": 1, "name": "Super Admin", "slug": "super_admin"},
        {"id": 2, "name": "Admin Akademik", "slug": "admin_akademik"},
        ...
    ]
}
```

### B. Lihat Permissions
```bash
curl -X GET http://127.0.0.1:8081/api/roles/permissions \
  -H "Authorization: Bearer $TOKEN"
```

### C. Lihat Role Management di Frontend
1. Buka: http://localhost:3000/role-management
2. Lihat daftar roles dan permissions
3. Coba assign permission ke role

---

## 🧑‍💼 STEP 4: TESTING PHASE 2 - PEGAWAI/HR SYSTEM

### A. Lihat Daftar Pegawai (API)
```bash
curl -X GET http://127.0.0.1:8081/api/pegawai \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

✅ Response: 20 pegawai yang sudah di-seed

### B. Cari Pegawai dengan Filter
```bash
# Search by name
curl -X GET "http://127.0.0.1:8081/api/pegawai?search=Ahmad" \
  -H "Authorization: Bearer $TOKEN"

# Filter by status
curl -X GET "http://127.0.0.1:8081/api/pegawai?status_kepegawaian=GTT" \
  -H "Authorization: Bearer $TOKEN"
```

### C. Lihat Detail Pegawai
```bash
curl -X GET http://127.0.0.1:8081/api/pegawai/1 \
  -H "Authorization: Bearer $TOKEN"
```

### D. Buat Pegawai Baru (API)
```bash
curl -X POST http://127.0.0.1:8081/api/pegawai \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "Bapak Novi Teguh",
    "jenis_kelamin": "L",
    "pendidikan_terakhir": "S1",
    "status_kepegawaian": "GTT",
    "jabatan": "Guru BK"
  }'
```

### E. Edit Pegawai
```bash
curl -X PUT http://127.0.0.1:8081/api/pegawai/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "Bapak Ahmad Fauzi (Updated)",
    "status_kepegawaian": "Tetap"
  }'
```

### F. Frontend - Pegawai Management
1. Login ke http://localhost:3000
2. Menu Sidebar → Kepegawaian → Data Pegawai
3. Lihat daftar pegawai
4. Coba: Filter, Cari, Buat Baru, Edit, Hapus

### G. Lihat Mata Pelajaran
```bash
curl -X GET http://127.0.0.1:8081/api/mata-pelajaran \
  -H "Authorization: Bearer $TOKEN"
```

✅ Response: 16 mata pelajaran

### H. Input Presensi Pegawai
```bash
curl -X POST http://127.0.0.1:8081/api/presensi-pegawai/batch \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tanggal": "2026-05-06",
    "records": [
      {"pegawai_id": 1, "status": "H"},
      {"pegawai_id": 2, "status": "I", "keterangan": "Izin"},
      {"pegawai_id": 3, "status": "S", "keterangan": "Sakit"}
    ]
  }'
```

---

## 📚 STEP 5: TESTING PHASE 3 - AKADEMIK & PENILAIAN

### A. Lihat Data Kelas
```bash
curl -X GET http://127.0.0.1:8081/api/kelas \
  -H "Authorization: Bearer $TOKEN"
```

✅ Response: 24 kelas sudah di-seed

### B. Lihat Santri
```bash
curl -X GET http://127.0.0.1:8081/api/santri?kelas_id=1 \
  -H "Authorization: Bearer $TOKEN"
```

### C. Input Penilaian (Nilai Santri)
```bash
curl -X POST http://127.0.0.1:8081/api/penilaian/submit \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "santri_id": 1,
    "periode": "2026-05",
    "jenis_nilai": "harian",
    "values": {
      "1": 85,
      "2": 88,
      "3": 90
    }
  }'
```

### D. Lihat Rekap Penilaian
```bash
curl -X GET "http://127.0.0.1:8081/api/penilaian/rekap?kelas_id=1&periode=2026-05" \
  -H "Authorization: Bearer $TOKEN"
```

### E. Export Penilaian ke Excel (Frontend)
1. http://localhost:3000/penilaian
2. Pilih kelas dan periode
3. Klik tombol "Export Excel"

### F. Input Presensi Siswa
```bash
curl -X POST http://127.0.0.1:8081/api/presensi-siswa/batch \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "kelas_id": 1,
    "tanggal": "2026-05-06",
    "records": [
      {"santri_id": 1, "status": "H"},
      {"santri_id": 2, "status": "I"},
      {"santri_id": 3, "status": "A"}
    ]
  }'
```

### G. Lihat Jadwal Pelajaran
```bash
curl -X GET http://127.0.0.1:8081/api/jadwal-pelajaran \
  -H "Authorization: Bearer $TOKEN"
```

### H. Frontend Testing - Penilaian
1. http://localhost:3000/penilaian
2. Pilih kategori penilaian (Harian, UTS, UAS, dst)
3. Pilih kelas & santri
4. Masukkan nilai per aspek
5. Klik "Simpan"
6. ✅ Lihat di "Rekap Penilaian"

---

## 🎓 STEP 6: TESTING PHASE 4 - CBT/UJIAN ONLINE

### A. Lihat Bank Soal
```bash
curl -X GET http://127.0.0.1:8081/api/bank-soal \
  -H "Authorization: Bearer $TOKEN"
```

✅ Response: Soal-soal yang sudah di-seed

### B. Filter Soal per Mata Pelajaran
```bash
curl -X GET "http://127.0.0.1:8081/api/bank-soal?mapel_id=1" \
  -H "Authorization: Bearer $TOKEN"
```

### C. Buat Ujian Baru (API)
```bash
curl -X POST http://127.0.0.1:8081/api/ujian \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "judul": "UTS Matematika Kelas 10A",
    "mapel_id": 1,
    "guru_id": 1,
    "kelas_ids": [1, 2],
    "durasi_menit": 60,
    "jumlah_soal": 10,
    "tanggal_mulai": "2026-05-10",
    "tanggal_selesai": "2026-05-10",
    "status": "draft",
    "shuffle_soal": true,
    "shuffle_jawaban": true,
    "soal_ids": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
  }'
```

### D. Generate Token Ujian
```bash
curl -X POST http://127.0.0.1:8081/api/ujian/1/generate-token \
  -H "Authorization: Bearer $TOKEN"
```

✅ Response:
```json
{
    "status": 200,
    "message": "Token generated",
    "data": {
        "ujian_id": 1,
        "token_akses": "ABC123DEF456"
    }
}
```

### E. Frontend - Manajemen Ujian
1. http://localhost:3000/cbt/ujian-manage
2. Klik "Buat Jadwal Ujian"
3. Isi form:
   - Judul ujian
   - Mata Pelajaran
   - Kelas (bisa multiple)
   - Durasi
   - Jumlah soal
4. Pilih soal dari bank soal
5. Klik "Simpan"
6. ✅ Lihat ujian di grid
7. Klik "Aktifkan & Generate Token"

### F. Frontend - Bank Soal
1. http://localhost:3000/cbt/bank-soal
2. Lihat daftar soal
3. Coba filter: Mata Pelajaran, Tingkat Kesulitan
4. Coba: Buat Soal Baru, Edit, Hapus

### G. Lihat Monitoring Ujian (Real-time)
```bash
curl -X GET http://127.0.0.1:8081/api/ujian/1/monitor \
  -H "Authorization: Bearer $TOKEN"
```

---

## 📊 STEP 7: TESTING DASHBOARD & STATISTIK

### A. Lihat Statistik Dashboard (API)
```bash
curl -X GET http://127.0.0.1:8081/api/dashboard/stats \
  -H "Authorization: Bearer $TOKEN"
```

✅ Response: Jumlah santri, kelas, pegawai, dll

### B. Lihat Dashboard di Frontend
1. http://localhost:3000/dashboard
2. Lihat cards: Total Santri, Kelas, Guru, Absensi
3. Lihat grafik (jika ada)

---

## 🛠️ STEP 8: TESTING DENGAN POSTMAN/INSOMNIA

### A. Import API Collection
1. Buka Postman/Insomnia
2. Buat collection baru: `SyIAR Gemilang`
3. Buat environment variable:
   ```
   base_url: http://127.0.0.1:8000
   token: (isi dengan token dari login)
   ```

### B. Buat Request Template
```
POST {{base_url}}/api/auth/login
Body (raw JSON):
{
  "username": "superadmin",
  "password": "Admin123!"
}
```

### C. Simpan Token
Dari response login, copy token ke environment variable `token`

### D. Buat Request dengan Auth
```
GET {{base_url}}/api/pegawai
Header:
  Authorization: Bearer {{token}}
```

---

## ✅ TESTING CHECKLIST

### Phase 1 - Authentication
- [ ] Login berhasil, dapat token JWT
- [ ] GET /api/auth/me berhasil
- [ ] Logout berfungsi
- [ ] Token expired handling

### Phase 2 - HR System
- [ ] GET /api/pegawai (list)
- [ ] POST /api/pegawai (create)
- [ ] PUT /api/pegawai/:id (update)
- [ ] DELETE /api/pegawai/:id (delete)
- [ ] GET /api/mata-pelajaran
- [ ] POST presensi pegawai batch
- [ ] Frontend pegawai list, form, detail

### Phase 3 - Academic
- [ ] GET /api/kelas, /api/santri
- [ ] POST penilaian/submit
- [ ] GET penilaian/rekap
- [ ] POST presensi-siswa/batch
- [ ] GET jadwal-pelajaran
- [ ] Frontend penilaian input & rekap
- [ ] Export Excel

### Phase 4 - CBT
- [ ] GET /api/bank-soal
- [ ] POST /api/ujian (create exam)
- [ ] GET /api/ujian/:id/generate-token
- [ ] Frontend bank soal list & form
- [ ] Frontend ujian manage
- [ ] Token generation & activation

---

## 🐛 TROUBLESHOOTING

### Server tidak berjalan
```bash
# Backend - apakah port 8081 terpakai?
lsof -i :8081
kill -9 <PID>

# Frontend - apakah port 3000 terpakai?
lsof -i :3000
kill -9 <PID>
```

### Database error
```bash
cd backend
php spark migrate:refresh
php spark db:seed InitialDataSeeder
php spark db:seed Phase2Seeder
php spark db:seed Phase3Seeder
php spark db:seed Phase4Seeder
php spark db:seed SantriSeeder
```

### Login gagal
```bash
# Cek database apakah user ada
mysql -u root -p syiar_gemilang
SELECT * FROM users;

# Reset password
php spark db:seed ResetAdminPasswordSeeder
```

### CORS Error (Frontend)
- Pastikan backend running di port 8000
- Cek CORS config di backend/app/Config/Routes.php
- Restart backend

### Frontend tidak muncul
```bash
# Install dependencies
cd frontend
npm install

# Clear node_modules & reinstall
rm -rf node_modules package-lock.json
npm install

# Restart server
npm start
```

---

## 📱 TESTING FLOW RECOMMENDATION

### Untuk Tester Baru:
1. **Day 1:** Phase 1 (Auth) + Phase 2 (HR)
2. **Day 2:** Phase 3 (Academic, Grading)
3. **Day 3:** Phase 4 (CBT)
4. **Day 4:** Integration & Edge Cases

### Test Order:
1. Backend API (Postman/curl)
2. Frontend UI (Browser)
3. Integration (End-to-end)
4. Performance
5. Security

---

## 🎯 TESTING TIPS

1. **Simpan Token:**
   ```bash
   TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"username":"superadmin","password":"Admin123!"}' | \
     jq -r '.data.token')
   echo $TOKEN
   ```

2. **Pretty JSON Output:**
   ```bash
   curl ... | python3 -m json.tool
   # atau
   curl ... | jq .
   ```

3. **Test Semua Role:**
   - superadmin (akses penuh)
   - guru1 (akses terbatas)

4. **Browser Developer Tools:**
   - F12 → Network tab untuk lihat API calls
   - Console untuk lihat errors
   - Application → LocalStorage untuk token

---

**Status:** ✅ Semua fitur siap ditest!  
**Backend API:** http://127.0.0.1:8081  
**Frontend:** http://localhost:3000
