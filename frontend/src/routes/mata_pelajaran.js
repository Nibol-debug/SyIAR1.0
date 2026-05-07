const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// List Mata Pelajaran
router.get('/', requireAuth, permission('mapel.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { kelompok } = req.query;
        const [mapelRes, aspekRes] = await Promise.all([
            client.get('/mata-pelajaran', { params: { kelompok } }),
            client.get('/aspek-penilaian')
        ]);
        
        res.render('pages/mata_pelajaran', {
            title: 'Mata Pelajaran',
            mapelList: mapelRes.data?.data || [],
            aspekList: aspekRes.data?.data || [],
            filters: { kelompok }
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat mata pelajaran');
        res.redirect('/dashboard');
    }
});

// Create
router.post('/', requireAuth, permission('mapel.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/mata-pelajaran', req.body);
        req.flash('success', 'Mata pelajaran ditambahkan');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menambahkan');
    }
    res.redirect('/mata-pelajaran');
});

// Update
router.post('/:id', requireAuth, permission('mapel.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.put(`/mata-pelajaran/${req.params.id}`, req.body);
        req.flash('success', 'Mata pelajaran diupdate');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal update');
    }
    res.redirect('/mata-pelajaran');
});

// Delete
router.delete('/:id', requireAuth, permission('mapel.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/mata-pelajaran/${req.params.id}`);
        res.json({ success: true, message: 'Dihapus' });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage || 'Gagal' });
    }
});

module.exports = router;
