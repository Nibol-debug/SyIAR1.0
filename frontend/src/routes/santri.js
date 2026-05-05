const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// ⚠️ STATIC routes MUST come before parameterized routes
// Otherwise /:id catches /create, /import, etc.

// Form Tambah Santri
router.get('/create', requireAuth, permission('santri.create'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const kelasRes = await client.get('/kelas');
        res.render('pages/santri_form', {
            title: 'Tambah Santri',
            mode: 'create',
            santri: {},
            kelas: kelasRes.data?.data || []
        });
    } catch (err) {
        console.error('Error loading form:', err.message);
        req.flash('error', 'Gagal memuat form');
        res.redirect('/santri');
    }
});

// Import CSV Form
router.get('/import', requireAuth, permission('santri.import'), (req, res) => {
    res.render('pages/santri_import', { title: 'Import Data Santri' });
});

// List Santri
router.get('/', requireAuth, permission('santri.read'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { search, kelas_id, status, page } = req.query;
        const [response, kelasRes] = await Promise.all([
            client.get('/santri', { params: { search, kelas_id, status, page } }),
            client.get('/kelas')
        ]);
        
        res.render('pages/santri_list', {
            title: 'Data Santri',
            santris: response.data?.data || [],
            pager: response.data?.pager,
            kelas: kelasRes.data?.data || [],
            filters: { search, kelas_id, status }
        });
    } catch (err) {
        console.error('Error loading santri:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat data santri');
        res.redirect('/dashboard');
    }
});

// Detail Santri
router.get('/:id', requireAuth, permission('santri.read'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const [santriRes, kelasRes] = await Promise.all([
            client.get(`/santri/${req.params.id}`),
            client.get('/kelas')
        ]);
        
        res.render('pages/santri_detail', {
            title: 'Detail Santri',
            santri: santriRes.data?.data,
            kelas: kelasRes.data?.data || []
        });
    } catch (err) {
        console.error('Error loading santri detail:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat detail');
        res.redirect('/santri');
    }
});

// Form Edit
router.get('/:id/edit', requireAuth, permission('santri.update'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const [santriRes, kelasRes] = await Promise.all([
            client.get(`/santri/${req.params.id}`),
            client.get('/kelas')
        ]);
        res.render('pages/santri_form', {
            title: 'Edit Santri',
            mode: 'edit',
            santri: santriRes.data?.data,
            kelas: kelasRes.data?.data || []
        });
    } catch (err) {
        console.error('Error loading edit form:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat data');
        res.redirect('/santri');
    }
});

// Process Create
router.post('/', requireAuth, permission('santri.create'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/santri', req.body);
        req.flash('success', 'Data santri berhasil ditambahkan');
        res.redirect('/santri');
    } catch (err) {
        console.error('Error creating santri:', err.message);
        req.flash('error', err.apiMessage || 'Gagal menambahkan data');
        res.redirect('/santri/create');
    }
});

// Process Update
router.post('/:id', requireAuth, permission('santri.update'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.put(`/santri/${req.params.id}`, req.body);
        req.flash('success', 'Data berhasil diupdate');
        res.redirect(`/santri/${req.params.id}`);
    } catch (err) {
        console.error('Error updating santri:', err.message);
        req.flash('error', err.apiMessage || 'Gagal mengupdate data');
        res.redirect(`/santri/${req.params.id}/edit`);
    }
});

// Delete (AJAX)
router.delete('/:id', requireAuth, permission('santri.delete'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/santri/${req.params.id}`);
        res.json({ success: true, message: 'Data berhasil dihapus' });
    } catch (err) {
        console.error('Error deleting santri:', err.message);
        res.status(400).json({ 
            success: false, 
            message: err.apiMessage || 'Gagal menghapus' 
        });
    }
});

// Process Import (placeholder)
router.post('/import', requireAuth, permission('santri.import'), async (req, res) => {
    try {
        req.flash('success', 'Fitur import CSV akan segera tersedia');
        res.redirect('/santri');
    } catch (err) {
        console.error('Error importing:', err.message);
        req.flash('error', err.message || 'Import gagal');
        res.redirect('/santri/import');
    }
});

module.exports = router;
