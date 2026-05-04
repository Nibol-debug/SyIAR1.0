# 📄 **LAPORAN PERUBAHAN & PENAMBAHAN FILE - PHASE 1**
## SyIAR Gemilang (Sistem Informasi Al-Azhar Rumah Gemilang)

---

## 📁 **BACKEND (CodeIgniter 4)**

### ✅ **File Baru Ditambahkan**

| No | Path File | Keterangan |
|----|-----------|-------------|
| 1 | `backend/app/Config/CORS.php` | Konfigurasi CORS untuk API |
| 2 | `backend/app/Config/Events.php` | Event handler (bersihkan CORS dari sini) |
| 3 | `backend/app/Config/Filters.php` | Registrasi filter Auth, Permission, CORS |
| 4 | `backend/app/Config/Routes.php` | Routing API (termasuk OPTIONS handler) |
| 5 | `backend/app/Controllers/Api/AuthController.php` | Controller untuk login, me, logout |
| 6 | `backend/app/Database/Migrations/2026-05-01-093152_CreateIntialTables.php` | Migration tabel users, roles, permissions, dll |
| 7 | `backend/app/Database/Migrations/2026-05-01-130409_CreateLoginLogsTable.php` | Migration tabel login_logs |
| 8 | `backend/app/Database/Seeds/InitialDataSeeder.php` | Seeder data awal (roles, permissions, users) |
| 9 | `backend/app/Filters/AuthFilter.php` | Filter autentikasi JWT |
| 10 | `backend/app/Filters/CORSOptionsFilter.php` | Filter untuk handle preflight OPTIONS |
| 11 | `backend/app/Filters/PermissionFilter.php` | Filter untuk cek permission |
| 12 | `backend/app/Helpers/jwt_helper.php` | Helper untuk generate & validate JWT |
| 13 | `backend/app/Models/LoginLogModel.php` | Model untuk tabel login_logs |
| 14 | `backend/app/Models/UserModel.php` | Model untuk tabel users |
| 15 | `backend/.env` | Environment variables (database, JWT, dll) |

### ✏️ **File yang Dimodifikasi**

| No | Path File | Perubahan |
|----|-----------|-----------|
| 1 | `backend/app/Config/Events.php` | Dihapus setting CORS (pindah ke filter) |
| 2 | `backend/app/Config/Filters.php` | Ditambahkan alias 'cors' dan global filters |
| 3 | `backend/composer.json` | Ditambahkan package `firebase/php-jwt` |
| 4 | `backend/.env.example` | Diconfig ulang (database, JWT secret) |
| 5 | `backend/public/index.php` | Tidak ada perubahan (bawaan) |
| 6 | `backend/spark` | Tidak ada perubahan |

---

## 📁 **FRONTEND (Express.js + Tailwind CSS)**

### ✅ **File Baru Ditambahkan**

| No | Path File | Keterangan |
|----|-----------|-------------|
| 1 | `frontend/package.json` | Dependencies & scripts |
| 2 | `frontend/package-lock.json` | Lock file dependencies |
| 3 | `frontend/tailwind.config.js` | Konfigurasi Tailwind CSS (custom colors) |
| 4 | `frontend/postcss.config.js` | Konfigurasi PostCSS (autoprefixer) |
| 5 | `frontend/server.js` | Main Express server |
| 6 | `frontend/.env` | Environment variables (PORT, API_URL, SESSION_SECRET) |
| 7 | `frontend/src/public/styles.css` | Source CSS Tailwind |
| 8 | `frontend/src/public/css/main.css` | Hasil build Tailwind (dihasilkan otomatis) |
| 9 | `frontend/src/views/layouts/main.ejs` | Layout utama (navbar, container) |
| 10 | `frontend/src/views/pages/login.ejs` | Halaman login dengan form & error handling |
| 11 | `frontend/src/views/pages/dashboard.ejs` | Halaman dashboard user |
| 12 | `frontend/src/views/pages/dashboard_full.ejs` | (Opsional) Dashboard full tanpa layout |

### ✏️ **File yang Dimodifikasi**

| No | Path File | Perubahan |
|----|-----------|-----------|
| 1 | `frontend/package.json` | Beberapa kali update script (build:css, dev:css) dari typo `tallwindcss` ke `tailwindcss` |
| 2 | `frontend/server.js` | Ditambahkan middleware security (helmet, XSS headers), error handling untuk network error |
| 3 | `frontend/src/views/pages/login.ejs` | Ditambahkan loading state, error message styling, XSS protection |
| 4 | `frontend/src/views/pages/dashboard.ejs` | Ditambahkan escaping output, debug info (development) |
| 5 | `frontend/.env` | Beberapa kali update (API_BASE_URL dari 8080 ke 8081) |

---

## 🗄️ **DATABASE**

### ✅ **Tabel yang Dibuat**

| No | Nama Tabel | Keterangan |
|----|------------|-------------|
| 1 | `users` | Data akun user |
| 2 | `roles` | Master role |
| 3 | `permissions` | Master permission |
| 4 | `user_roles` | Mapping user ke role |
| 5 | `role_permissions` | Mapping role ke permission |
| 6 | `login_logs` | Log aktivitas login |
| 7 | `migrations` | (Bawaan CI4) Tabel riwayat migration |

### ✅ **Data yang Diseed**

| No | Tabel | Jumlah Data |
|----|-------|--------------|
| 1 | `roles` | 4 roles (super_admin, admin_akademik, guru, kepala_bagian) |
| 2 | `permissions` | 14 permissions (user CRUD, role manage, penilaian, master data) |
| 3 | `users` | 2 users (superadmin, guru1) |
| 4 | `user_roles` | 2 mapping |
| 5 | `role_permissions` | 14 mapping (super_admin dapat semua permission) |

---

## 🔧 **KONFIGURASI YANG DIUBAH**

### **Backend (.env)**
```diff
+ CI_ENVIRONMENT = development
+ database.default.hostname = localhost
+ database.default.database = 34_Sistem_informasiSIARG
+ database.default.username = root
+ database.default.password = tkjtkj
+ JWT_SECRET_KEY = "SyIAR_S3cr3t_K3y_2026_!@#$%^&*"
+ JWT_ACCESS_TOKEN_EXPIRE = 7200
```

### **Frontend (.env)**
```diff
+ PORT=3000
+ API_BASE_URL=http://localhost:8081/api
+ SESSION_SECRET=syiar-frontend-secret-key-2026
+ NODE_ENV=development
```

### **Package.json Scripts**
```diff
"scripts": {
-   "test": "echo \"Error: no test specified\" && exit 1"
+   "build:css": "npx tailwindcss -i ./src/public/styles.css -o ./src/public/css/main.css --minify",
+   "dev:css": "npx tailwindcss -i ./src/public/styles.css -o ./src/public/css/main.css --watch",
+   "start": "node server.js",
+   "dev": "concurrently \"nodemon server.js\" \"npm run dev:css\""
}
```

---

## 📊 **RINGKASAN PERUBAHAN**

| Komponen | File Baru | File Modifikasi | Total |
|----------|-----------|-----------------|-------|
| **Backend (CI4)** | 15 | 6 | 21 |
| **Frontend (Express)** | 12 | 5 | 17 |
| **Database** | 7 tabel | - | 7 |
| **Konfigurasi** | 3 file | 2 file | 5 |
| **TOTAL** | **37** | **13** | **50** |

---

## 🎯 **STATUS FINAL**

✅ **Semua file Phase 1 telah dibuat/dimodifikasi sesuai kebutuhan**  
✅ **Backend siap dengan JWT auth, CORS, database**  
✅ **Frontend siap dengan Tailwind CSS, session, error handling**  
✅ **Integrasi frontend-backend berfungsi normal**  

**Kesimpulan:** Semua perubahan dan penambahan file telah didokumentasikan dan siap untuk Phase 2.

---

**Laporan ini dibuat untuk keperluan dokumentasi tim pengembangan.**  
📅 *3 Mei 2026*  
👨‍💻 *Tim Pengembang SyIAR Gemilang*
