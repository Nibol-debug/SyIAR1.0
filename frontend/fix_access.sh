#!/bin/bash

echo "=========================================="
echo "  PERBAIKAN AKSES ROLE MANAGEMENT"
echo "=========================================="

cd /var/www/html/34/SyIAR1.0/frontend

# 1. Buat middleware
mkdir -p src/middleware
cat > src/middleware/auth.js << 'EOF'
module.exports = {
    isAuthenticated: (req, res, next) => {
        if (!req.session.user) {
            return res.redirect('/login');
        }
        next();
    },
    isSuperAdmin: (req, res, next) => {
        if (!req.session.user) {
            return res.redirect('/login');
        }
        const roles = req.session.user.roles || [];
        if (!roles.includes('super_admin')) {
            return res.status(403).send(`
                <html>
                <head><title>Akses Ditolak</title></head>
                <body style="font-family: sans-serif; text-align: center; padding: 50px;">
                    <h1 style="color: red;">⛔ Akses Ditolak!</h1>
                    <p>Halaman ini hanya bisa diakses oleh Super Admin.</p>
                    <a href="/dashboard">Kembali ke Dashboard</a>
                </body>
                </html>
            `);
        }
        next();
    }
};
EOF

# 2. Update server.js - tambahkan import middleware
if ! grep -q "require('./src/middleware/auth')" server.js; then
    sed -i "/require('express-ejs-layouts');/a const { isAuthenticated, isSuperAdmin } = require('./src/middleware/auth');" server.js
fi

# 3. Update route /admin/roles dengan middleware
sed -i "s|app.get('/admin/roles', async (req, res)|app.get('/admin/roles', isAuthenticated, isSuperAdmin, async (req, res)|g" server.js

# 4. Restart frontend
pkill -f "node server.js"
npm run dev > /dev/null 2>&1 &

echo ""
echo "=========================================="
echo "✅ SELESAI!"
echo "=========================================="
echo ""
echo "Sekarang:"
echo "  ✅ Super Admin → Bisa akses /admin/roles"
echo "  ❌ Role lain → Dapat pesan 'Akses Ditolak'"
echo ""
