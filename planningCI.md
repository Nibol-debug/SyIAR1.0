# 📋 Planning Development: SyIAR Gemilang
## System Informasi Akademik Rumah Gemilang Indonesia

---

## 🎯 1. Overview Proyek

| Item | Deskripsi |
|------|-----------|
| **Nama Aplikasi** | SyIAR Gemilang (ERP Sekolah) |
| **Arsitektur** | Decoupled: CI4 REST API Backend + Express.js SSR Frontend |
| **Scope** | Modul 1–5 dari Proposal ERP |
| **Target User** | Admin, Kepala Sekolah, Guru, Wali Kelas, Staf TU, Siswa, Orang Tua |
| **Kapasitas** | 1.300 santri + puluhan guru & staf |
| **Lingkungan** | Node.js 18+, PHP 8.3+, MySQL 8.x / MariaDB 11.x |

---

## 🛠 2. Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Frontend** | Express.js + EJS + Tailwind CSS |
| **Backend API** | CodeIgniter 4 (RESTful JSON) |
| **Database** | MySQL 8.x / MariaDB 11.x |
| **Auth** | JWT (firebase/php-jwt) |
| **HTTP Client** | Axios (Express → CI4) |

```
Browser → Express.js (EJS + Tailwind) → Axios → CI4 REST API → MySQL
              ↑                                                  ↓
      Cookie/Session ←─────────────── JWT Token ←──── JSON Response
```

---

## 📦 3. Scope Modul (Dari Proposal)

| # | Modul | Status |
|---|-------|--------|
| 1 | **Modul Santri** (Student Management) | ✅ Dasar sudah ada |
| 2 | **Modul Manajemen Pengguna & ACL** (RBAC) | ✅ Sudah ada |
| 3 | **Modul Kepegawaian** (HRM Guru & Staf) | ❌ Belum |
| 4 | **Modul Akademik** (Kurikulum, Jadwal, Absensi, PPDB) | 🔶 PPDB dasar ada |
| 5 | **Modul Ujian Online** (CBT - Computer Based Test) | ❌ Belum |
| - | **Modul Penilaian** (Nilai, Rapor, KKM) | 🔶 Dasar ada |

> **Catatan:** Modul 6 (Keuangan/SPP), 7 (Inventaris), 8 (Dashboard Multi-Portal) **TIDAK termasuk** scope saat ini.

---

## 🗓 4. Roadmap Fase Pengembangan

### Phase 1 — Fondasi & Manajemen Kesiswaan ✅ (SELESAI)
> *Modul 1 + Modul 2 — Database, Auth, RBAC, Data Santri*

**Backend (CI4):**
- JWT Auth (login, logout, /me)
- RBAC: roles, permissions, role_permissions, user_roles
- CRUD Santri (NIS auto-generate, search, filter by kelas/jurusan/status)
- CRUD Kelas (nama_kelas, jurusan, tahun_ajaran, wali_kelas, kapasitas)
- Import CSV santri massal (1.300 data)
- Dashboard Stats API

**Frontend (Express):**
- Login page + session management
- Sidebar navigation + role-based menu
- Manajemen Santri (list, create, edit, detail, import)
- Manajemen Kelas (list, CRUD)
- Role & Permission Management (super_admin only)
- Dashboard dengan statistik

**Database Tables:**
| Tabel | Keterangan |
|-------|-----------|
| `users` | Akun login (username, email, password, nama_lengkap) |
| `roles` | Master role (super_admin, kepala_sekolah, guru, wali_kelas, staf_tu, siswa, orang_tua) |
| `permissions` | Hak akses granular (santri.create, kelas.manage, dll) |
| `role_permissions` | Many-to-many role ↔ permission |
| `user_roles` | Many-to-many user ↔ role |
| `santris` | Data santri (NIS, NISN, biodata, orang tua, kelas_id, status) |
| `kelas` | Data kelas (nama, **jurusan**, tahun_ajaran, wali, kapasitas) |
| `login_logs` | Audit trail login |

---

### Phase 2 — Kepegawaian / HRM ❌ (BELUM)
> *Modul 3 — Data Guru, Staf, Dokumen, Presensi Staf*

**Backend — Tabel Baru:**
| Tabel | Kolom Utama |
|-------|------------|
| `pegawai` | user_id, nip, nuptk, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_telepon, email, pendidikan_terakhir, gelar, universitas, tahun_lulus, status_kepegawaian (GTT/PTT/Tetap), tanggal_bergabung, foto |
| `pegawai_dokumen` | pegawai_id, jenis_dokumen (ijazah/sertifikasi/sk), nama_file, file_path, uploaded_at |
| `presensi_pegawai` | pegawai_id, tanggal, jam_masuk, jam_keluar, status (hadir/izin/sakit/alpha), keterangan |
| `mata_pelajaran` | kode_mapel, nama_mapel, kelompok (wajib/peminatan/muatan_lokal) |
| `pegawai_mapel` | pegawai_id, mapel_id (mapping guru ↔ mata pelajaran) |

**Backend — API Endpoints:**
```
GET/POST        /api/pegawai              — List & Create pegawai
GET/PUT/DELETE   /api/pegawai/:id          — Detail, Update, Delete
POST             /api/pegawai/:id/dokumen  — Upload dokumen
GET              /api/pegawai/:id/dokumen  — List dokumen
GET/POST         /api/presensi-pegawai     — Rekap & input presensi
GET              /api/mata-pelajaran       — CRUD mata pelajaran
GET              /api/pegawai/statistik    — Dashboard personalia
```

**Frontend — Views:**
- `pegawai_list.ejs` — Daftar guru & staf + filter status/pendidikan
- `pegawai_form.ejs` — Form tambah/edit pegawai
- `pegawai_detail.ejs` — Profil + tab dokumen + riwayat presensi
- `presensi_pegawai.ejs` — Input & rekap presensi bulanan
- `mata_pelajaran.ejs` — CRUD mata pelajaran

**Permissions Baru:**
`pegawai.create`, `pegawai.read`, `pegawai.update`, `pegawai.delete`, `presensi_pegawai.manage`, `mapel.manage`

---

### Phase 3 — Akademik & PPDB 🔶 (SEBAGIAN)
> *Modul 4 (Akademik) + PPDB Online*

**Backend — Tabel Baru/Update:**
| Tabel | Kolom Utama |
|-------|------------|
| `tahun_ajaran` | kode (2025/2026), nama, tanggal_mulai, tanggal_selesai, is_active |
| `jadwal_pelajaran` | kelas_id, mapel_id, guru_id, hari, jam_mulai, jam_selesai, ruangan |
| `presensi_siswa` | santri_id, kelas_id, mapel_id, tanggal, status (H/I/S/A), pencatat_id |
| `jurnal_mengajar` | guru_id, kelas_id, mapel_id, tanggal, materi, tugas, keterangan |
| `kalender_akademik` | judul, tanggal_mulai, tanggal_selesai, jenis (ujian/libur/kegiatan), deskripsi |
| `ppdb_registrations` | ✅ Sudah ada — perlu enhancement (verifikasi berkas, pengumuman) |

**Backend — API Endpoints:**
```
# Akademik
CRUD  /api/tahun-ajaran
CRUD  /api/jadwal-pelajaran         — + deteksi bentrok otomatis
CRUD  /api/presensi-siswa           — Input per kelas + rekap
CRUD  /api/jurnal-mengajar          — Input per pertemuan
CRUD  /api/kalender-akademik

# PPDB (enhance existing)
GET   /api/ppdb                     — List pendaftar + filter status
POST  /api/ppdb/register            — Pendaftaran publik (no auth)
PUT   /api/ppdb/:id/verify          — Verifikasi berkas admin
PUT   /api/ppdb/:id/status          — Update status (accepted/rejected)
POST  /api/ppdb/:id/convert-santri  — Convert calon → santri aktif + generate NIS
GET   /api/ppdb/statistik           — Stats pendaftaran
```

**Frontend — Views:**
- `jadwal_pelajaran.ejs` — Grid jadwal per kelas/guru
- `presensi_siswa.ejs` — Input absensi per kelas + rekap bulanan
- `jurnal_mengajar.ejs` — Form jurnal harian guru
- `kalender_akademik.ejs` — Kalender visual kegiatan
- `ppdb_public.ejs` — Landing page pendaftaran (publik, tanpa login)
- `ppdb_admin.ejs` — Panel verifikasi + pengumuman

**Permissions Baru:**
`jadwal.manage`, `presensi_siswa.create`, `presensi_siswa.read`, `jurnal.manage`, `kalender.manage`, `ppdb.verify`, `ppdb.manage`

---

### Phase 4 — CBT & Penilaian Terintegrasi 🔶 (PENILAIAN DASAR ADA)
> *Modul 5 (CBT/Ujian Online) + Modul Penilaian (enhance)*

**Backend — Tabel Baru:**
| Tabel | Kolom Utama |
|-------|------------|
| `bank_soal` | mapel_id, guru_id, pertanyaan, tipe_soal (pg/pg_kompleks/menjodohkan/isian/esai), pilihan_jawaban (JSON), kunci_jawaban, tingkat_kesulitan, topik, is_active |
| `ujian` | judul, mapel_id, kelas_ids (JSON), guru_id, durasi_menit, jumlah_soal, tanggal_mulai, tanggal_selesai, token_akses, shuffle_soal, shuffle_jawaban, max_peringatan, status (draft/aktif/selesai) |
| `ujian_soal` | ujian_id, soal_id, urutan, bobot_nilai |
| `ujian_sesi` | ujian_id, santri_id, waktu_mulai, waktu_selesai, status (belum/mengerjakan/selesai/diskualifikasi), peringatan_count, ip_address, user_agent |
| `ujian_jawaban` | sesi_id, soal_id, jawaban_siswa, is_benar, nilai_manual, pengoreksi_id |
| `ujian_pelanggaran` | sesi_id, jenis (tab_switch/minimize/copy_paste), waktu, detail |

**Anti-Cheat Features:**
- Focus Tracking (detect tab switch / minimize)
- Auto-Submit setelah X peringatan
- Fullscreen Enforcement
- Shuffle soal & jawaban per siswa
- Token unik per sesi ujian
- Device locking (1 akun = 1 device saat ujian)

**Backend — API Endpoints:**
```
# Bank Soal
CRUD  /api/bank-soal                 — Kelola soal per mapel
GET   /api/bank-soal/filter          — Filter by mapel/tingkat/topik

# Ujian
CRUD  /api/ujian                     — Kelola ujian
POST  /api/ujian/:id/generate-token  — Generate token akses
GET   /api/ujian/:id/monitor         — Real-time monitoring pengawas

# Sesi Ujian (endpoint siswa)
POST  /api/ujian/mulai               — Mulai ujian (validasi token)
GET   /api/ujian/sesi/:id/soal       — Ambil soal (shuffled)
POST  /api/ujian/sesi/:id/jawab      — Simpan jawaban
POST  /api/ujian/sesi/:id/submit     — Submit ujian
POST  /api/ujian/sesi/:id/pelanggaran — Catat pelanggaran

# Penilaian (enhance existing)
GET   /api/penilaian/import-cbt/:ujian_id  — Tarik nilai CBT → buku nilai
POST  /api/penilaian/hitung-akhir          — Hitung nilai akhir (weighted)
GET   /api/penilaian/rapor/:santri_id      — Generate data rapor
GET   /api/penilaian/analisis-soal/:ujian_id — Analisis butir soal
```

**Enhance Modul Penilaian (existing):**
- ✅ Kategori & Aspek Penilaian (sudah ada)
- ✅ Input batch nilai per santri (sudah ada)
- ✅ Rekap per kelas (sudah ada)
- ❌ Integrasi otomatis nilai CBT → buku nilai
- ❌ Weighted Scoring (30% Harian, 30% UTS, 40% UAS)
- ❌ Logika KKM + deskripsi otomatis
- ❌ Manajemen Remedial
- ❌ E-Rapor cetak PDF

**Frontend — Views:**
- `bank_soal.ejs` — CRUD soal + filter + preview
- `ujian_manage.ejs` — Kelola ujian (guru)
- `ujian_monitor.ejs` — Panel monitoring real-time (pengawas)
- `ujian_siswa.ejs` — Interface ujian siswa (fullscreen, anti-cheat)
- `rapor_generate.ejs` — Generate & cetak rapor PDF
- `analisis_soal.ejs` — Statistik butir soal

**Permissions Baru:**
`bank_soal.manage`, `ujian.create`, `ujian.monitor`, `ujian.ikut`, `rapor.generate`, `rapor.cetak`

---

## 🔐 5. Role & Permission Matrix

| Role | Akses Utama |
|------|------------|
| **super_admin** | Akses PENUH ke seluruh modul + server + backup |
| **kepala_sekolah** | READ-ONLY semua laporan (akademik, kepegawaian) |
| **guru** | Bank soal, pelaksanaan CBT, input nilai harian, absensi kelas |
| **wali_kelas** | Rekap absensi, grafik nilai, cetak E-Rapor kelas perwalian |
| **staf_tu** | Data santri, PPDB, kepegawaian |
| **siswa** | Ikut ujian CBT, lihat jadwal, cek nilai & absensi |
| **orang_tua** | Monitor nilai anak, absensi, status tagihan |

---

## 🗄 6. Ringkasan Database

### Tables per Phase

**Phase 1 (✅ Done):** `users`, `roles`, `permissions`, `role_permissions`, `user_roles`, `santris`, `kelas`, `login_logs`

**Phase 2 (TODO):** `pegawai`, `pegawai_dokumen`, `presensi_pegawai`, `mata_pelajaran`, `pegawai_mapel`

**Phase 3 (TODO):** `tahun_ajaran`, `jadwal_pelajaran`, `presensi_siswa`, `jurnal_mengajar`, `kalender_akademik`, `ppdb_registrations` (enhance)

**Phase 4 (TODO):** `bank_soal`, `ujian`, `ujian_soal`, `ujian_sesi`, `ujian_jawaban`, `ujian_pelanggaran`, `kategori_penilaian` (✅), `aspek_penilaian` (✅), `penilaian` (enhance)

**Total: ~25 tabel**

---

## 📝 7. Catatan Perubahan dari Sistem Lama

1. **`tingkat` → `jurusan`** — Field `tingkat` (SD/SMP/SMA) di tabel `kelas` diganti menjadi `jurusan` sebagai identitas utama kelas
2. **Kelas format** — Contoh: `10A - IPA`, `7A - Reguler`, `1A - Umum`
3. **PPDB Enhancement** — Dari CRUD sederhana → multi-step form publik + verifikasi berkas + convert ke santri
4. **Penilaian Enhancement** — Dari input manual → integrasi CBT + weighted scoring + KKM + rapor PDF

---

## 🚀 8. Urutan Pengerjaan (Prioritas)

```
Phase 1 ✅ ──→ Phase 2 ──→ Phase 3 ──→ Phase 4
Santri+RBAC    HRM Guru     Akademik     CBT+Penilaian
(DONE)         +Staf        +PPDB        +Rapor
```

> **Strategi:** Phase 1 & 2 harus selesai dulu karena data guru dibutuhkan sebagai FK di jadwal, presensi, dan bank soal. Phase 3 (PPDB) bisa di-deploy duluan untuk musim pendaftaran.
