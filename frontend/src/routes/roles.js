const express = require('express');
const router = express.Router();
const { api } = require('../services/apiClient');
const { requireAuth, isSuperAdmin, injectUserToViews } = require('../middleware/auth');

router.use(injectUserToViews);

// Role Management page
router.get('/', requireAuth, isSuperAdmin, async (req, res) => {
    try {
        const [rolesRes, permsRes] = await Promise.all([
            api.get('/roles', {}, req.session.token),
            api.get('/roles/permissions', {}, req.session.token)
        ]);

        res.render('pages/role_management', {
            title: 'Manajemen Role & Hak Akses',
            roles: rolesRes.data?.data || [],
            all_permissions: permsRes.data?.data || [],
            token: req.session.token || ''
        });
    } catch (err) {
        console.error('Error loading roles:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat data role');
        res.redirect('/dashboard');
    }
});

module.exports = router;
