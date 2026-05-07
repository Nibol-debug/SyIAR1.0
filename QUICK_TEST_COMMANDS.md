# 📋 QUICK REFERENCE - TESTING COMMANDS
## Copy-Paste Commands untuk Testing Cepat

---

## ✅ STEP 1: Setup & Login

### Terminal 1 - Start Backend
```bash
cd backend
php spark serve --host 127.0.0.1 --port 8000
```

### Terminal 2 - Start Frontend
```bash
cd frontend
npm start
```

### Terminal 3 - Get Token (copy seluruh command ini)
```bash
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"superadmin","password":"Admin123!"}' | \
  python3 -c "import sys, json; print(json.load(sys.stdin).get('data', {}).get('token', ''))")
echo "Token saved: $TOKEN"
```

---

## 🧪 TESTING COMMANDS (Copy-Paste Ready)

### 1. HEALTH CHECK
```bash
curl http://127.0.0.1:8000/health | python3 -m json.tool
```

### 2. LOGIN & GET TOKEN
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"superadmin","password":"Admin123!"}' | python3 -m json.tool
```

### 3. GET PROFILE
```bash
curl -X GET http://127.0.0.1:8000/api/auth/me \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

---

## 👥 PHASE 2 - HR / PEGAWAI

### List Pegawai
```bash
curl -X GET http://127.0.0.1:8000/api/pegawai \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool | head -50
```

### Search Pegawai
```bash
curl -X GET "http://127.0.0.1:8000/api/pegawai?search=Ahmad" \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Get Detail Pegawai
```bash
curl -X GET http://127.0.0.1:8000/api/pegawai/1 \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Create Pegawai Baru
```bash
curl -X POST http://127.0.0.1:8000/api/pegawai \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "Ibu Nurani Wijaya",
    "jenis_kelamin": "P",
    "pendidikan_terakhir": "S1",
    "status_kepegawaian": "GTT",
    "jabatan": "Guru Umum"
  }' | python3 -m json.tool
```

### Update Pegawai
```bash
curl -X PUT http://127.0.0.1:8000/api/pegawai/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status_kepegawaian": "Tetap",
    "jabatan": "Guru Senior"
  }' | python3 -m json.tool
```

### Mata Pelajaran
```bash
curl -X GET http://127.0.0.1:8000/api/mata-pelajaran \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Input Presensi Pegawai (Batch)
```bash
curl -X POST http://127.0.0.1:8000/api/presensi-pegawai/batch \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tanggal": "2026-05-06",
    "records": [
      {"pegawai_id": 1, "status": "H"},
      {"pegawai_id": 2, "status": "I", "keterangan": "Izin"},
      {"pegawai_id": 3, "status": "S", "keterangan": "Sakit"}
    ]
  }' | python3 -m json.tool
```

---

## 📚 PHASE 3 - AKADEMIK

### List Kelas
```bash
curl -X GET http://127.0.0.1:8000/api/kelas \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### List Santri (limit 5)
```bash
curl -X GET "http://127.0.0.1:8000/api/santri?limit=5" \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Get Santri by Kelas
```bash
curl -X GET "http://127.0.0.1:8000/api/santri?kelas_id=1" \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Submit Penilaian (Nilai Santri)
```bash
curl -X POST http://127.0.0.1:8000/api/penilaian/submit \
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
  }' | python3 -m json.tool
```

### Get Rekap Penilaian
```bash
curl -X GET "http://127.0.0.1:8000/api/penilaian/rekap?kelas_id=1&periode=2026-05" \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Input Presensi Siswa (Batch)
```bash
curl -X POST http://127.0.0.1:8000/api/presensi-siswa/batch \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "kelas_id": 1,
    "tanggal": "2026-05-06",
    "records": [
      {"santri_id": 1, "status": "H"},
      {"santri_id": 2, "status": "I", "keterangan": "Izin"},
      {"santri_id": 3, "status": "A", "keterangan": "Alpa"}
    ]
  }' | python3 -m json.tool
```

### Jadwal Pelajaran
```bash
curl -X GET http://127.0.0.1:8000/api/jadwal-pelajaran \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

---

## 🎓 PHASE 4 - CBT / UJIAN

### List Bank Soal
```bash
curl -X GET http://127.0.0.1:8000/api/bank-soal \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Filter Soal by Mata Pelajaran
```bash
curl -X GET "http://127.0.0.1:8000/api/bank-soal?mapel_id=1" \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### List Ujian
```bash
curl -X GET http://127.0.0.1:8000/api/ujian \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Create Ujian Baru
```bash
curl -X POST http://127.0.0.1:8000/api/ujian \
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
  }' | python3 -m json.tool
```

### Generate Token Ujian
```bash
curl -X POST http://127.0.0.1:8000/api/ujian/1/generate-token \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Monitor Ujian (Real-time)
```bash
curl -X GET http://127.0.0.1:8000/api/ujian/1/monitor \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

---

## 📊 STATISTIK & DASHBOARD

### Dashboard Stats
```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/stats \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

### Statistik Pegawai
```bash
curl -X GET http://127.0.0.1:8000/api/pegawai/statistik \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
```

---

## 🌐 BROWSER TESTING

### Frontend URLs
| Halaman | URL |
|---------|-----|
| Login | http://localhost:3000/login |
| Dashboard | http://localhost:3000/dashboard |
| Pegawai | http://localhost:3000/pegawai |
| Presensi Pegawai | http://localhost:3000/presensi-pegawai |
| Santri | http://localhost:3000/santri |
| Kelas | http://localhost:3000/kelas |
| Penilaian | http://localhost:3000/penilaian |
| Penilaian Input | http://localhost:3000/penilaian/input |
| Presensi Siswa | http://localhost:3000/presensi |
| Bank Soal | http://localhost:3000/cbt/bank-soal |
| Ujian Manage | http://localhost:3000/cbt/ujian-manage |
| Role Management | http://localhost:3000/role-management |

### Test Users
```
Username: superadmin
Password: Admin123!

OR

Username: guru1
Password: Guru123!
```

---

## 🔧 TROUBLESHOOTING COMMANDS

### Reset Database
```bash
cd backend
php spark migrate:refresh
php spark db:seed InitialDataSeeder
php spark db:seed Phase2Seeder
php spark db:seed Phase3Seeder
php spark db:seed Phase4Seeder
php spark db:seed SantriSeeder
```

### Reset Admin Password
```bash
cd backend
php spark db:seed ResetAdminPasswordSeeder
```

### Kill Port 8000 (if stuck)
```bash
lsof -i :8000 | grep LISTEN | awk '{print $2}' | xargs kill -9
```

### Kill Port 3000 (if stuck)
```bash
lsof -i :3000 | grep LISTEN | awk '{print $2}' | xargs kill -9
```

### Clear Logs
```bash
rm backend/writable/logs/*.log
```

---

## 📌 TIPS & TRICKS

### 1. Save Token ke Environment
```bash
export TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"superadmin","password":"Admin123!"}' | \
  python3 -c "import sys, json; print(json.load(sys.stdin).get('data', {}).get('token', ''))")
```

### 2. Test all endpoints quickly
```bash
cd /var/www/html/34/SyIARG-tkj34-G-R\ 2
bash test_api.sh
```

### 3. Pretty Print JSON
```bash
# Using python
curl ... | python3 -m json.tool

# Using jq (install: apt-get install jq)
curl ... | jq .
```

### 4. Save Response to File
```bash
curl -X GET http://127.0.0.1:8000/api/pegawai \
  -H "Authorization: Bearer $TOKEN" > pegawai_list.json
```

### 5. Check Response Headers
```bash
curl -i http://127.0.0.1:8000/health
```

### 6. Measure Response Time
```bash
curl -w "\nTime: %{time_total}s\n" http://127.0.0.1:8000/health
```

---

## ✨ TESTING CHECKLIST

### Phase 1 - Auth
- [ ] Health check working
- [ ] Login successful, get token
- [ ] Get profile (/me) working
- [ ] Logout working
- [ ] Invalid login rejected

### Phase 2 - HR
- [ ] List pegawai
- [ ] Create pegawai
- [ ] Update pegawai
- [ ] Delete pegawai
- [ ] Search pegawai
- [ ] List mata pelajaran
- [ ] Input presensi pegawai

### Phase 3 - Academic
- [ ] List kelas
- [ ] List santri
- [ ] Submit penilaian
- [ ] Get rekap penilaian
- [ ] Input presensi siswa
- [ ] List jadwal pelajaran

### Phase 4 - CBT
- [ ] List bank soal
- [ ] Filter soal
- [ ] Create ujian
- [ ] Generate token
- [ ] Monitor ujian

---

**Last Updated:** 6 Mei 2026  
**Status:** ✅ All endpoints tested and working
