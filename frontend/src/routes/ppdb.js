const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// PPDB List
router.get('/', requireAuth, permission('ppdb.read'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { status } = req.query;
        const [ppdbRes, statsRes] = await Promise.all([
            client.get('/ppdb', { params: { status } }),
            client.get('/ppdb/stats')
        ]);
        
        res.render('pages/ppdb', {
            title: 'PPDB Online',
            registrations: ppdbRes.data?.data || [],
            stats: statsRes.data?.data || [],
            filters: { status }
        });
    } catch (err) {
        console.error('Error loading PPDB:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat data PPDB');
        res.redirect('/dashboard');
    }
});

module.exports = router;
