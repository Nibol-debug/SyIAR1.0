/**
 * SyIAR Gemilang - Frontend Server (Express.js)
 * Phase 1-3: Auth, RBAC, Santri, Kelas, Penilaian, PPDB
 */

require('dotenv').config();
const express = require('express');
const session = require('express-session');
const flash = require('connect-flash');
const cookieParser = require('cookie-parser');
const path = require('path');
const axios = require('axios');

const app = express();
const PORT = process.env.PORT || 3000;
const API_BASE_URL = process.env.API_BASE_URL || 'http://localhost:8081/api';

// ============================================
// MIDDLEWARE SETUP
// ============================================

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());

app.use(session({
    secret: process.env.SESSION_SECRET || 'syiar-secret-key-dev-2026',
    resave: false,
    saveUninitialized: false,
    cookie: { 
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'lax',
        maxAge: 24 * 60 * 60 * 1000
    }
}));

app.use(flash());

// Global locals for views
app.use((req, res, next) => {
    res.locals.success = req.flash('success');
    res.locals.error = req.flash('error');
    res.locals.user = req.session.user || null;
    res.locals.permissions = req.session.permissions || [];
    res.locals.isAuthenticated = !!req.session.user;
    res.locals.token = req.session.token || '';
    next();
});

// View engine
app.set('views', path.join(__dirname, 'src/views'));
app.set('view engine', 'ejs');

// Static files
app.use(express.static(path.join(__dirname, 'src/public')));

// ============================================
// IMPORT MIDDLEWARE & ROUTES
// ============================================

let authMiddleware, permissionMiddleware;
try {
    authMiddleware = require('./src/middleware/auth');
    permissionMiddleware = require('./src/middleware/permission');
    console.log('✅ Middleware loaded');
} catch (err) {
    console.warn('⚠️  Using fallback middleware:', err.message);
    authMiddleware = {
        requireAuth: (req, res, next) => req.session?.user ? next() : res.redirect('/login'),
        isSuperAdmin: (req, res, next) => next(),
        injectUserToViews: (req, res, next) => next()
    };
    permissionMiddleware = (perm) => (req, res, next) => next();
    permissionMiddleware.any = (perms) => (req, res, next) => next();
}

let santriRoutes, roleRoutes, kelasRoutes, penilaianRoutes, ppdbRoutes, pegawaiRoutes, presensiRoutes;
try {
    santriRoutes = require('./src/routes/santri');
    roleRoutes = require('./src/routes/roles');
    kelasRoutes = require('./src/routes/kelas');
    penilaianRoutes = require('./src/routes/penilaian');
    ppdbRoutes = require('./src/routes/ppdb');
    pegawaiRoutes = require('./src/routes/pegawai');
    presensiRoutes = require('./src/routes/presensi');
    console.log('✅ Routes loaded');
} catch (err) {
    console.warn('⚠️  Route loading error:', err.message);
    const fallbackRouter = () => { const r = express.Router(); return r; };
    santriRoutes = fallbackRouter();
    roleRoutes = fallbackRouter();
    kelasRoutes = fallbackRouter();
    penilaianRoutes = fallbackRouter();
    ppdbRoutes = fallbackRouter();
    pegawaiRoutes = fallbackRouter();
    presensiRoutes = fallbackRouter();
}

// ============================================
// PUBLIC ROUTES
// ============================================

app.get('/health', (req, res) => {
    res.json({ 
        status: 'ok', 
        timestamp: new Date().toISOString(),
        uptime: process.uptime()
    });
});

// Root redirect
app.get('/', (req, res) => {
    res.redirect(req.session?.user ? '/dashboard' : '/login');
});

// Login page
app.get('/login', (req, res) => {
    if (req.session?.user) return res.redirect('/dashboard');
    res.render('pages/login', { 
        title: 'Login',
        redirect: req.query.redirect || '/dashboard'
    });
});

// Process login
app.post('/login', async (req, res) => {
    try {
        const { username, password, redirect } = req.body;
        
        if (!username || !password) {
            req.flash('error', 'Username dan password wajib diisi');
            return res.redirect('/login');
        }
        
        const response = await axios.post(`${API_BASE_URL}/auth/login`, {
            username,
            password
        }, {
            headers: { 'Content-Type': 'application/json' },
            timeout: 15000
        });
        
        const { token, user, permissions } = response.data.data || {};
        
        if (!token || !user) {
            throw new Error('Invalid response from API');
        }
        
        // Save to session
        req.session.token = token;
        req.session.user = user;
        req.session.permissions = Array.isArray(permissions) ? permissions : [];
        
        req.flash('success', `Selamat datang, ${user.nama_lengkap || user.username}!`);
        res.redirect(redirect || '/dashboard');
        
    } catch (err) {
        console.error('Login error:', err.response?.data || err.message);
        const msg = err.response?.data?.messages?.error || err.response?.data?.message || err.message || 'Login gagal';
        req.flash('error', typeof msg === 'string' ? msg : JSON.stringify(msg));
        res.redirect('/login');
    }
});

// Logout
app.get('/logout', (req, res) => {
    const username = req.session?.user?.username;
    req.session.destroy((err) => {
        if (err) console.error('Logout error:', err);
        res.clearCookie('connect.sid');
        console.log(`👋 User ${username} logged out`);
        res.redirect('/login?logged_out=1');
    });
});

// ============================================
// PROTECTED ROUTES
// ============================================

// Dashboard
app.get('/dashboard', authMiddleware.requireAuth, async (req, res) => {
    let stats = null;
    try {
        const { createApiClient } = require('./src/services/apiClient');
        const client = createApiClient(req.session.token);
        const statsRes = await client.get('/dashboard/stats');
        stats = statsRes.data?.data || null;
    } catch (err) {
        // Stats API optional, dashboard still renders with null stats
    }
    
    res.render('pages/dashboard', {
        title: 'Dashboard',
        user: req.session.user,
        permissions: req.session.permissions,
        stats
    });
});

// Module routes
app.use('/santri', santriRoutes);
app.use('/admin/roles', roleRoutes);
app.use('/kelas', kelasRoutes);
app.use('/penilaian', penilaianRoutes);
app.use('/ppdb', ppdbRoutes);
app.use('/pegawai', pegawaiRoutes);
app.use('/presensi', presensiRoutes);

// ============================================
// ERROR HANDLING
// ============================================

app.use((req, res) => {
    res.status(404).render('pages/error', {
        title: '404 - Not Found',
        message: 'Halaman yang Anda cari tidak ditemukan',
        code: 404,
        user: res.locals.user
    });
});

app.use((err, req, res, next) => {
    console.error('❌ Server error:', err.message);
    
    if (err.code === 'ECONNREFUSED') {
        return res.status(503).render('pages/error', {
            title: 'Service Unavailable',
            message: 'Tidak dapat terhubung ke backend API. Pastikan server CI4 berjalan.',
            code: 503,
            user: res.locals.user
        });
    }
    
    const isApiRequest = req.xhr || req.headers.accept?.includes('application/json');
    
    if (isApiRequest) {
        return res.status(err.status || 500).json({
            success: false,
            message: process.env.NODE_ENV === 'development' ? err.message : 'Server error'
        });
    }
    
    res.status(err.status || 500).render('pages/error', {
        title: 'Server Error',
        message: process.env.NODE_ENV === 'development' ? err.message : 'Terjadi kesalahan server',
        code: err.status || 500,
        user: res.locals.user
    });
});

// ============================================
// START SERVER
// ============================================

app.listen(PORT, '0.0.0.0', () => {
    console.log(`
╔════════════════════════════════════════╗
║   🚀 SyIAR Frontend Ready!            ║
╠════════════════════════════════════════╣
║   🌐 URL: http://localhost:${PORT}
║   📡 API:  ${API_BASE_URL}
║   🔧 Env:  ${process.env.NODE_ENV || 'development'}
║   🕐 Time: ${new Date().toLocaleString('id-ID')}
╚════════════════════════════════════════╝
    `);
});

process.on('SIGINT', () => {
    console.log('\n👋 Shutting down gracefully...');
    process.exit(0);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('❌ Unhandled Rejection:', reason);
});

module.exports = app;
