
---

## ✅ **Step 2: Buat Struktur Folder dan File**

```bash
# Buat folder structure
mkdir -p src/views/layouts
mkdir -p src/views/pages
mkdir -p src/routes
mkdir -p src/middleware
mkdir -p src/services
mkdir -p src/public/css
mkdir -p src/public/js

# Buat file CSS Tailwind
cat > src/public/styles.css << 'EOF'
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  .btn-primary {
    @apply bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-200;
  }
  .btn-secondary {
    @apply bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200;
  }
  .form-input {
    @apply w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500;
  }
  .card {
    @apply bg-white rounded-lg shadow-md p-6;
  }
}
EOF
```

---

## ✅ **Step 3: Update package.json**

```bash
cat > package.json << 'EOF'
{
  "name": "syiar-frontend",
  "version": "1.0.0",
  "description": "Frontend for SyIAR Gemilang",
  "main": "server.js",
  "scripts": {
    "build:css": "tailwindcss -i ./src/public/styles.css -o ./src/public/css/main.css --minify",
    "dev:css": "tailwindcss -i ./src/public/styles.css -o ./src/public/css/main.css --watch",
    "start": "node server.js",
    "dev": "concurrently \"nodemon server.js\" \"npm run dev:css\""
  },
  "dependencies": {
    "axios": "^1.6.0",
    "cookie-parser": "^1.4.6",
    "dotenv": "^16.3.0",
    "ejs": "^3.1.9",
    "express": "^4.18.2",
    "express-session": "^1.17.3"
  },
  "devDependencies": {
    "autoprefixer": "^10.4.16",
    "concurrently": "^8.2.2",
    "nodemon": "^3.0.1",
    "postcss": "^8.4.31",
    "tailwindcss": "^3.3.5"
  }
}
EOF
```

---

## ✅ **Step 4: Buat Express Server**

```bash
cat > server.js << 'EOF'
require('dotenv').config();
const express = require('express');
const session = require('express-session');
const cookieParser = require('cookie-parser');
const path = require('path');
const axios = require('axios');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(express.static('src/public'));

// Session configuration
app.use(session({
    secret: process.env.SESSION_SECRET || 'default-secret-change-this',
    resave: false,
    saveUninitialized: false,
    cookie: {
        secure: process.env.NODE_ENV === 'production',
        httpOnly: true,
        maxAge: 24 * 60 * 60 * 1000
    }
}));

// View engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'src/views'));

// Make user available in all views
app.use((req, res, next) => {
    res.locals.user = req.session.user || null;
    res.locals.currentPath = req.path;
    next();
});

// Routes
app.get('/', (req, res) => {
    if (req.session.user) {
        res.redirect('/dashboard');
    } else {
        res.redirect('/login');
    }
});

app.get('/login', (req, res) => {
    if (req.session.user) {
        return res.redirect('/dashboard');
    }
    res.render('pages/login', { title: 'Login', error: null });
});

app.post('/login', async (req, res) => {
    try {
        const response = await axios.post(`${process.env.API_BASE_URL || 'http://localhost:8081/api'}/auth/login`, {
            username: req.body.username,
            password: req.body.password
        });
        
        if (response.data.status === 'success') {
            req.session.token = response.data.data.token;
            req.session.user = response.data.data.user;
            req.session.permissions = response.data.data.permissions || [];
            
            res.redirect('/dashboard');
        } else {
            res.render('pages/login', { 
                title: 'Login', 
                error: response.data.message 
            });
        }
    } catch (error) {
        const errorMsg = error.response?.data?.message || 'Login gagal, silakan coba lagi';
        res.render('pages/login', { title: 'Login', error: errorMsg });
    }
});

app.get('/dashboard', async (req, res) => {
    if (!req.session.token) {
        return res.redirect('/login');
    }
    
    try {
        const response = await axios.get(`${process.env.API_BASE_URL || 'http://localhost:8081/api'}/auth/me`, {
            headers: { Authorization: `Bearer ${req.session.token}` }
        });
        
        res.render('pages/dashboard', { 
            title: 'Dashboard',
            user: response.data.data.user
        });
    } catch (error) {
        req.session.destroy();
        res.redirect('/login');
    }
});

app.get('/logout', (req, res) => {
    req.session.destroy();
    res.redirect('/login');
});

// Start server
app.listen(PORT, () => {
    console.log(`✅ Frontend running on http://localhost:${PORT}`);
    console.log(`📡 API connected to ${process.env.API_BASE_URL || 'http://localhost:8081/api'}`);
});
EOF
```

---

## ✅ **Step 5: Buat EJS Templates**

```bash
# Layout main
cat > src/views/layouts/main.ejs << 'EOF'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><%= title %> - SyIAR Gemilang</title>
    <link href="/css/main.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-primary-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold">SyIAR Gemilang</a>
                <% if (user) { %>
                <div class="flex items-center gap-4">
                    <span>Halo, <%= user.nama_lengkap %></span>
                    <a href="/logout" class="bg-red-600 px-3 py-1 rounded hover:bg-red-700">Logout</a>
                </div>
                <% } %>
            </div>
        </div>
    </nav>
    
    <main class="container mx-auto px-4 py-8">
        <%- body %>
    </main>
</body>
</html>
EOF

# Login page
cat > src/views/pages/login.ejs << 'EOF'
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Login SyIAR Gemilang</h1>
    
    <% if (error) { %>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <%= error %>
        </div>
    <% } %>
    
    <form method="POST" action="/login">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Username / Email</label>
            <input type="text" name="username" required 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Password</label>
            <input type="password" name="password" required 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        
        <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition">
            Login
        </button>
    </form>
</div>
EOF

# Dashboard page
cat > src/views/pages/dashboard.ejs << 'EOF'
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Selamat Datang</h3>
        <p class="text-gray-600"><%= user.nama_lengkap %></p>
        <p class="text-gray-600 text-sm"><%= user.email %></p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Role</h3>
        <p class="text-gray-600"><%= user.roles ? user.roles.join(', ') : 'User' %></p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Status</h3>
        <p class="text-green-600">✅ Aktif</p>
    </div>
</div>

<div class="mt-6 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4">Informasi Sistem</h2>
    <p>Anda berhasil login ke Sistem Informasi Al-Azhar Rumah Gemilang (SyIAR).</p>
    <p class="mt-2">Fitur yang tersedia akan muncul sesuai dengan role dan permission Anda.</p>
</div>
EOF
```

---

## ✅ **Step 6: Buat File .env**

```bash
cat > .env << 'EOF'
PORT=3000
API_BASE_URL=http://localhost:8081/api
SESSION_SECRET=syiar-frontend-secret-key-2026
NODE_ENV=development
EOF
```

---

## ✅ **Step 7: Build CSS dan Jalankan Server**

```bash
# Build CSS Tailwind
npm run build:css

# Jalankan server
npm run dev
```

---

## 🚀 **Akses Aplikasi**

Buka browser dan akses: **http://localhost:3000**

Login dengan:
- **Username:** superadmin
- **Password:** Admin123!

---

## ✅ **Troubleshooting jika masih error**

```bash
# Cek apakah semua package terinstall
npm list --depth=0

# Reinstall jika perlu
rm -rf node_modules package-lock.json
npm install

# Build CSS manual
npx tailwindcss -i ./src/public/styles.css -o ./src/public/css/main.css --minify

# Jalankan server tanpa CSS watch dulu
node server.js
```

**Setelah semua berjalan, Anda akan melihat halaman login yang cantik dengan Tailwind CSS!** 🎉
