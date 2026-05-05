const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// List Pegawai
router.get('/', requireAuth, permission('pegawai.read'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { search, status_kepegawaian } = req.query;
        
        const response = await client.get('/pegawai', { params: { search, status_kepegawaian } });
        
        res.render('pages/pegawai_list', {
            title: 'Data Pegawai',
            pegawaiList: response.data?.data || [],
            filters: { search, status_kepegawaian }
        });
    } catch (err) {
        console.error('Error loading pegawai:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat data pegawai');
        res.redirect('/dashboard');
    }
});

// Create form
router.get('/create', requireAuth, permission('pegawai.create'), (req, res) => {
    res.render('pages/pegawai_form', {
        title: 'Tambah Pegawai',
        mode: 'create',
        pegawai: {}
    });
});

// Detail
router.get('/:id', requireAuth, permission('pegawai.read'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const response = await client.get(`/pegawai/${req.params.id}`);
        res.render('pages/pegawai_detail', {
            title: 'Detail Pegawai',
            pegawai: response.data?.data
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat detail');
        res.redirect('/pegawai');
    }
});

// Create POST
router.post('/', requireAuth, permission('pegawai.create'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/pegawai', req.body);
        req.flash('success', 'Pegawai berhasil ditambahkan');
        res.redirect('/pegawai');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menambahkan');
        res.redirect('/pegawai/create');
    }
});

// Update POST
router.post('/:id', requireAuth, permission('pegawai.update'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.put(`/pegawai/${req.params.id}`, req.body);
        req.flash('success', 'Pegawai berhasil diupdate');
        res.redirect(`/pegawai/${req.params.id}`);
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal update');
        res.redirect(`/pegawai/${req.params.id}`);
    }
});

// Delete AJAX
router.delete('/:id', requireAuth, permission('pegawai.delete'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/pegawai/${req.params.id}`);
        res.json({ success: true, message: 'Pegawai berhasil dihapus' });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage || 'Gagal menghapus' });
    }
});

module.exports = router;
