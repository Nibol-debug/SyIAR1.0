/**
 * Middleware Permission Check untuk Express.js Frontend
 * Mengecek permission user dari session
 */

// Cek single permission
const permission = (requiredPermission) => {
    return (req, res, next) => {
        const permissions = req.session?.permissions || [];
        const userRole = req.session?.user?.role;
        
        // Super admin bisa akses semua
        if (userRole === 'super_admin') {
            return next();
        }
        
        if (permissions.includes(requiredPermission)) {
            return next();
        }
        
        // Jika request AJAX/API
        if (req.xhr || req.headers.accept?.includes('application/json')) {
            return res.status(403).json({ 
                success: false, 
                message: 'Forbidden: Permission denied' 
            });
        }
        
        // Redirect dengan flash message
        req.flash('error', `Akses ditolak: Anda tidak memiliki izin "${requiredPermission}"`);
        res.redirect('/dashboard');
    };
};

// Cek multiple permissions (OR logic - cukup salah satu)
permission.any = (permissions) => {
    return (req, res, next) => {
        const userPerms = req.session?.permissions || [];
        const userRole = req.session?.user?.role;
        
        if (userRole === 'super_admin') return next();
        if (permissions.some(p => userPerms.includes(p))) return next();
        
        req.flash('error', 'Akses ditolak: Permission tidak mencukupi');
        res.redirect('/dashboard');
    };
};

// Cek multiple permissions (AND logic - harus semua)
permission.all = (permissions) => {
    return (req, res, next) => {
        const userPerms = req.session?.permissions || [];
        const userRole = req.session?.user?.role;
        
        if (userRole === 'super_admin') return next();
        if (permissions.every(p => userPerms.includes(p))) return next();
        
        req.flash('error', 'Akses ditolak: Tidak semua permission tersedia');
        res.redirect('/dashboard');
    };
};

// Helper untuk EJS: cek permission di view
permission.check = (requiredPermission, userPermissions, userRole) => {
    if (userRole === 'super_admin') return true;
    return Array.isArray(userPermissions) && userPermissions.includes(requiredPermission);
};

module.exports = permission;
