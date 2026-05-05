const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// List Kelas
router.get('/', requireAuth, permission('kelas.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const response = await client.get('/kelas', { params: req.query });
        res.render('pages/kelas_list', {
            title: 'Manajemen Kelas',
            kelasList: response.data?.data || [],
            filters: req.query
        });
    } catch (err) {
        console.error('Error loading kelas:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat data kelas');
        res.redirect('/dashboard');
    }
});

// Create Kelas
router.post('/', requireAuth, permission('kelas.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/kelas', req.body);
        req.flash('success', 'Kelas berhasil ditambahkan');
        res.redirect('/kelas');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menambah kelas');
        res.redirect('/kelas');
    }
});

// Update Kelas
router.post('/:id', requireAuth, permission('kelas.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.put(`/kelas/${req.params.id}`, req.body);
        req.flash('success', 'Kelas berhasil diupdate');
        res.redirect('/kelas');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal update kelas');
        res.redirect('/kelas');
    }
});

// Delete Kelas (AJAX)
router.delete('/:id', requireAuth, permission('kelas.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/kelas/${req.params.id}`);
        res.json({ success: true, message: 'Kelas berhasil dihapus' });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage || 'Gagal menghapus' });
    }
});

module.exports = router;
