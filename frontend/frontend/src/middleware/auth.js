module.exports = {
    // Cek apakah user sudah login
    isAuthenticated: (req, res, next) => {
        if (!req.session.user) {
            return res.redirect('/login');
        }
        next();
    },
    
    // Cek apakah user punya role super_admin
    isSuperAdmin: (req, res, next) => {
        if (!req.session.user) {
            return res.redirect('/login');
        }
        
        const roles = req.session.user.roles || [];
        if (!roles.includes('super_admin')) {
            return res.status(403).send('Akses ditolak! Hanya Super Admin yang bisa mengakses halaman ini.');
        }
        next();
    }
};
