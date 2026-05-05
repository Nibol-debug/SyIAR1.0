const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// Presensi input page
router.get('/', requireAuth, permission.any(['presensi_siswa.create', 'presensi_siswa.read']), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { kelas_id, tanggal } = req.query;
        
        const kelasRes = await client.get('/kelas');
        let presensiData = [];
        
        if (kelas_id) {
            const presensiRes = await client.get('/presensi-siswa', { params: { kelas_id, tanggal: tanggal || new Date().toISOString().slice(0, 10) } });
            presensiData = presensiRes.data?.data || [];
        }
        
        res.render('pages/presensi_siswa', {
            title: 'Presensi Siswa',
            kelas: kelasRes.data?.data || [],
            presensiData,
            filters: { kelas_id, tanggal: tanggal || new Date().toISOString().slice(0, 10) }
        });
    } catch (err) {
        console.error('Error loading presensi:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat presensi');
        res.redirect('/dashboard');
    }
});

// Submit batch presensi
router.post('/save', requireAuth, permission('presensi_siswa.create'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { kelas_id, tanggal, mapel_id } = req.body;
        
        // Build records from form data
        const records = [];
        const statusFields = req.body.status || {};
        const keteranganFields = req.body.keterangan_item || {};
        
        Object.keys(statusFields).forEach(santriId => {
            records.push({
                santri_id: parseInt(santriId),
                status: statusFields[santriId],
                keterangan: keteranganFields[santriId] || null,
            });
        });
        
        await client.post('/presensi-siswa/batch', {
            kelas_id: parseInt(kelas_id),
            tanggal,
            mapel_id: mapel_id || null,
            records
        });
        
        req.flash('success', `Presensi berhasil disimpan (${records.length} santri)`);
        res.redirect(`/presensi?kelas_id=${kelas_id}&tanggal=${tanggal}`);
    } catch (err) {
        console.error('Error saving presensi:', err.response?.data || err.message);
        req.flash('error', err.apiMessage || 'Gagal menyimpan presensi');
        res.redirect('/presensi');
    }
});

// Rekap presensi
router.get('/rekap', requireAuth, permission('presensi_siswa.read'), async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const { kelas_id, bulan } = req.query;
        
        const kelasRes = await client.get('/kelas');
        let rekap = [];
        
        if (kelas_id) {
            const rekapRes = await client.get('/presensi-siswa/rekap', { params: { kelas_id, bulan: bulan || new Date().toISOString().slice(0, 7) } });
            rekap = rekapRes.data?.data || [];
        }
        
        res.render('pages/presensi_rekap', {
            title: 'Rekap Presensi',
            kelas: kelasRes.data?.data || [],
            rekap,
            filters: { kelas_id, bulan: bulan || new Date().toISOString().slice(0, 7) }
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat rekap');
        res.redirect('/presensi');
    }
});

module.exports = router;
