const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// Input
router.get('/', requireAuth, permission('presensi_pegawai.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { tanggal } = req.query;
        const response = await client.get('/presensi-pegawai', { params: { tanggal: tanggal || new Date().toISOString().slice(0, 10) } });
        
        res.render('pages/presensi_pegawai', {
            title: 'Presensi Pegawai',
            presensiData: response.data?.data || [],
            filters: { tanggal: response.data?.tanggal || new Date().toISOString().slice(0, 10) }
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat presensi');
        res.redirect('/dashboard');
    }
});

// Save Batch
router.post('/save', requireAuth, permission('presensi_pegawai.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { tanggal } = req.body;
        const statusFields = req.body.status || {};
        const jamMasuk = req.body.jam_masuk || {};
        const jamKeluar = req.body.jam_keluar || {};
        const keteranganFields = req.body.keterangan_item || {};
        
        const records = Object.keys(statusFields).map(id => ({
            pegawai_id: parseInt(id),
            status: statusFields[id],
            jam_masuk: jamMasuk[id] || null,
            jam_keluar: jamKeluar[id] || null,
            keterangan: keteranganFields[id] || null
        }));
        
        await client.post('/presensi-pegawai/batch', { tanggal, records });
        req.flash('success', `Presensi disimpan (${records.length} pegawai)`);
        res.redirect(`/presensi-pegawai?tanggal=${tanggal}`);
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menyimpan');
        res.redirect('/presensi-pegawai');
    }
});

// Rekap
router.get('/rekap', requireAuth, permission('presensi_pegawai.manage'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { bulan } = req.query;
        const response = await client.get('/presensi-pegawai/rekap', { params: { bulan: bulan || new Date().toISOString().slice(0, 7) } });
        
        res.render('pages/presensi_pegawai_rekap', {
            title: 'Rekap Presensi Pegawai',
            rekap: response.data?.data || [],
            filters: { bulan: response.data?.bulan || new Date().toISOString().slice(0, 7) }
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat rekap');
        res.redirect('/presensi-pegawai');
    }
});

module.exports = router;
