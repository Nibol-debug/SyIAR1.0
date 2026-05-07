const express = require('express');
const router = express.Router();
const { createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');

router.use(injectUserToViews);
router.use(requireAuth);

// ================= TAHUN AJARAN =================
router.get('/tahun-ajaran', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const response = await client.get('/tahun-ajaran');
        res.render('pages/akademik/tahun_ajaran', {
            title: 'Tahun Ajaran',
            tahunAjaran: response.data || []
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat tahun ajaran');
        res.redirect('/dashboard');
    }
});
router.post('/tahun-ajaran', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/tahun-ajaran', req.body);
        req.flash('success', 'Tahun ajaran disimpan');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menyimpan');
    }
    res.redirect('/akademik/tahun-ajaran');
});
router.delete('/tahun-ajaran/:id', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/tahun-ajaran/${req.params.id}`);
        res.json({ success: true });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage });
    }
});

// ================= JADWAL PELAJARAN =================
router.get('/jadwal-pelajaran', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const [jadwalRes, kelasRes, mapelRes, guruRes] = await Promise.all([
            client.get('/jadwal-pelajaran'),
            client.get('/kelas'),
            client.get('/mata-pelajaran'),
            client.get('/pegawai')
        ]);
        res.render('pages/akademik/jadwal_pelajaran', {
            title: 'Jadwal Pelajaran',
            jadwal: jadwalRes.data || [],
            kelasList: kelasRes.data?.data || [],
            mapelList: mapelRes.data?.data || [],
            guruList: guruRes.data?.data || []
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat jadwal');
        res.redirect('/dashboard');
    }
});
router.post('/jadwal-pelajaran', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/jadwal-pelajaran', req.body);
        req.flash('success', 'Jadwal berhasil ditambahkan');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menyimpan');
    }
    res.redirect('/akademik/jadwal-pelajaran');
});
router.delete('/jadwal-pelajaran/:id', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/jadwal-pelajaran/${req.params.id}`);
        res.json({ success: true });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage });
    }
});

// ================= JURNAL MENGAJAR =================
router.get('/jurnal-mengajar', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const [jurnalRes, kelasRes, mapelRes, guruRes] = await Promise.all([
            client.get('/jurnal-mengajar'),
            client.get('/kelas'),
            client.get('/mata-pelajaran'),
            client.get('/pegawai')
        ]);
        res.render('pages/akademik/jurnal_mengajar', {
            title: 'Jurnal Mengajar',
            jurnal: jurnalRes.data || [],
            kelasList: kelasRes.data?.data || [],
            mapelList: mapelRes.data?.data || [],
            guruList: guruRes.data?.data || []
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat jurnal');
        res.redirect('/dashboard');
    }
});
router.post('/jurnal-mengajar', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/jurnal-mengajar', req.body);
        req.flash('success', 'Jurnal berhasil ditambahkan');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menyimpan');
    }
    res.redirect('/akademik/jurnal-mengajar');
});
router.delete('/jurnal-mengajar/:id', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/jurnal-mengajar/${req.params.id}`);
        res.json({ success: true });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage });
    }
});

// ================= KALENDER AKADEMIK =================
router.get('/kalender-akademik', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const response = await client.get('/kalender-akademik');
        res.render('pages/akademik/kalender_akademik', {
            title: 'Kalender Akademik',
            kalender: response.data || []
        });
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal memuat kalender');
        res.redirect('/dashboard');
    }
});
router.post('/kalender-akademik', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.post('/kalender-akademik', req.body);
        req.flash('success', 'Agenda berhasil ditambahkan');
    } catch (err) {
        req.flash('error', err.apiMessage || 'Gagal menyimpan');
    }
    res.redirect('/akademik/kalender-akademik');
});
router.delete('/kalender-akademik/:id', async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        await client.delete(`/kalender-akademik/${req.params.id}`);
        res.json({ success: true });
    } catch (err) {
        res.status(400).json({ success: false, message: err.apiMessage });
    }
});

module.exports = router;
