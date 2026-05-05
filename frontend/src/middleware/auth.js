/**
 * Middleware Authentication untuk Express.js Frontend
 * Mengecek session user dari backend CI4
 */

// Cek apakah user sudah login (ada session user)
const requireAuth = (req, res, next) => {
    if (req.session && req.session.user) {
        return next();
    }
    
    // Jika request AJAX/API, return JSON error
    if (req.xhr || req.headers.accept?.includes('application/json')) {
        return res.status(401).json({ 
            success: false, 
            message: 'Unauthorized: Please login first' 
        });
    }
    
    // Redirect ke login page
    req.flash('error', 'Silakan login terlebih dahulu');
    res.redirect('/login?redirect=' + encodeURIComponent(req.originalUrl));
};

// Cek apakah user adalah super_admin
// Supports both: user.role = 'super_admin' (string) and user.roles = [{slug: 'super_admin'}] (array)
const isSuperAdmin = (req, res, next) => {
    if (!req.session?.user) {
        return res.redirect('/login');
    }
    
    const user = req.session.user;
    const isSA = user.role === 'super_admin' 
        || (Array.isArray(user.roles) && user.roles.some(r => r.slug === 'super_admin' || r === 'super_admin'));
    
    if (isSA) {
        return next();
    }
    
    req.flash('error', 'Akses ditolak: Hanya Super Admin yang dapat mengakses halaman ini');
    res.redirect('/dashboard');
};

// Cek role user (bisa multiple roles)
const hasRole = (allowedRoles) => {
    return (req, res, next) => {
        if (!req.session?.user) {
            return res.redirect('/login');
        }
        
        const user = req.session.user;
        const userRoles = [];
        
        // Collect all role slugs
        if (user.role) userRoles.push(user.role);
        if (Array.isArray(user.roles)) {
            user.roles.forEach(r => {
                if (typeof r === 'string') userRoles.push(r);
                else if (r.slug) userRoles.push(r.slug);
            });
        }
        
        const allowed = Array.isArray(allowedRoles) ? allowedRoles : [allowedRoles];
        if (userRoles.some(r => allowed.includes(r))) {
            return next();
        }
        
        req.flash('error', 'Akses ditolak: Role tidak sesuai');
        res.redirect('/dashboard');
    };
};

// Inject user & permissions ke locals untuk EJS views
const injectUserToViews = (req, res, next) => {
    if (req.session?.user) {
        res.locals.user = req.session.user;
        res.locals.permissions = req.session.permissions || [];
        res.locals.isAuthenticated = true;
        res.locals.token = req.session.token || '';
    } else {
        res.locals.user = null;
        res.locals.permissions = [];
        res.locals.isAuthenticated = false;
        res.locals.token = '';
    }
    next();
};

module.exports = {
    requireAuth,
    isSuperAdmin,
    hasRole,
    injectUserToViews
};
