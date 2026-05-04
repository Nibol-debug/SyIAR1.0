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
