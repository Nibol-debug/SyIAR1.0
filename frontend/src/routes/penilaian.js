const express = require('express');
const router = express.Router();
const { api, createApiClient } = require('../services/apiClient');
const { requireAuth, injectUserToViews } = require('../middleware/auth');
const permission = require('../middleware/permission');

router.use(injectUserToViews);

// Penilaian Dashboard
router.get('/', requireAuth, permission.any(['penilaian.create', 'penilaian.read']), async (req, res) => {
    try {
        const token = req.session.token;
        const client = createApiClient(token);
        
        const [kelasRes, kategoriRes] = await Promise.all([
            client.get('/kelas'),
            client.get('/kategori-penilaian?active_only=1')
        ]);
        
        res.render('pages/penilaian', {
            title: 'Modul Penilaian',
            kelas: kelasRes.data?.data || [],
            kategori: kategoriRes.data?.data || []
        });
    } catch (err) {
        console.error('Error loading penilaian:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat modul penilaian');
        res.redirect('/dashboard');
    }
});

// Input Penilaian Page (GET - loads form with aspek)
router.get('/input', requireAuth, permission.any(['penilaian.create']), async (req, res) => {
    try {
        const token = req.session.token;
        const client = createApiClient(token);
        const { santri_id, kelas_id, periode } = req.query;
        
        // Always load kelas list
        const kelasRes = await client.get('/kelas');
        const kelasList = kelasRes.data?.data || [];
        
        let santriList = [];
        let aspekGrouped = {};
        let selectedSantri = null;
        
        // If kelas selected, load santri for that kelas
        if (kelas_id) {
            const santriRes = await client.get('/santri', { params: { kelas_id } });
            santriList = santriRes.data?.data || [];
        }
        
        // If santri selected, load all aspek penilaian grouped by kategori
        if (santri_id) {
            try {
                const aspekRes = await client.get('/aspek-penilaian');
                const aspekAll = aspekRes.data?.data || [];
                
                // Group aspek by kategori
                aspekAll.forEach(function(a) {
                    const katNama = a.nama_kategori || 'Lainnya';
                    if (!aspekGrouped[katNama]) {
                        aspekGrouped[katNama] = {
                            warna: a.warna || '#3b82f6',
                            aspek: []
                        };
                    }
                    aspekGrouped[katNama].aspek.push(a);
                });
            } catch (aspekErr) {
                console.error('Error loading aspek:', aspekErr.response?.status, aspekErr.message);
            }
            
            // Find selected santri info
            selectedSantri = santriList.find(s => String(s.id) === String(santri_id));
        }
        
        res.render('pages/penilaian_input', {
            title: 'Input Penilaian',
            kelas: kelasList,
            santriList,
            aspekGrouped,
            selectedKelasId: kelas_id || '',
            selectedSantriId: santri_id || '',
            selectedSantri,
            periode: periode || new Date().toISOString().slice(0, 7),
            success: req.flash('success'),
            error: req.flash('error')
        });
    } catch (err) {
        console.error('Error loading penilaian input:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat form penilaian');
        res.redirect('/penilaian');
    }
});

// Submit Penilaian (POST)
router.post('/input/submit', requireAuth, permission('penilaian.create'), async (req, res) => {
    try {
        const token = req.session.token;
        const client = createApiClient(token);
        const { santri_id, periode, keterangan, is_draft } = req.body;
        
        // Extract values from form (values[aspek_id] = nilai)
        let values = {};
        if (req.body.values && typeof req.body.values === 'object') {
            values = req.body.values;
        } else {
            // Fallback for extended: false
            Object.keys(req.body).forEach(key => {
                if (key.startsWith('values[')) {
                    const id = key.match(/\[(.*?)\]/)[1];
                    values[id] = req.body[key];
                }
            });
        }
        
        if (!santri_id || Object.keys(values).length === 0) {
            req.flash('error', 'Data penilaian tidak lengkap');
            return res.redirect('/penilaian/input?santri_id=' + santri_id);
        }
        
        // Convert select values like "A","B","C","D" to numeric
        const numericValues = {};
        Object.entries(values).forEach(([aspekId, nilai]) => {
            if (nilai === '') return; // Skip empty
            const map = { 'A': 90, 'B': 75, 'C': 60, 'D': 40 };
            numericValues[aspekId] = map[nilai] !== undefined ? map[nilai] : parseFloat(nilai) || 0;
        });
        
        await client.post('/penilaian/submit', {
            santri_id: parseInt(santri_id),
            values: numericValues,
            periode: periode || new Date().toISOString().slice(0, 7),
            keterangan: keterangan || null,
            is_draft: is_draft === 'true'
        });
        
        req.flash('success', is_draft === 'true' ? 'Draft penilaian berhasil disimpan!' : 'Penilaian berhasil disimpan!');
        res.redirect('/penilaian/input?santri_id=' + santri_id + '&kelas_id=' + (req.body.kelas_id || '') + '&periode=' + (periode || ''));
    } catch (err) {
        console.error('Error submitting penilaian:', err.response?.data || err.message);
        req.flash('error', err.apiMessage || 'Gagal menyimpan penilaian');
        res.redirect('/penilaian/input?santri_id=' + req.body.santri_id);
    }
});

// Rekap Penilaian
router.get('/rekap', requireAuth, permission('penilaian.read'), async (req, res) => {
    try {
        const token = req.session.token;
        const client = createApiClient(token);
        const { kelas_id, periode } = req.query;
        let rekap = [];
        
        if (kelas_id) {
            const rekapRes = await client.get('/penilaian/rekap', { params: { kelas_id, periode } });
            rekap = rekapRes.data?.data || [];
        }
        
        const kelasRes = await client.get('/kelas');
        
        res.render('pages/penilaian_rekap', {
            title: 'Rekap Penilaian',
            rekap,
            kelas: kelasRes.data?.data || [],
            filters: { kelas_id, periode: periode || new Date().toISOString().slice(0, 7) }
        });
    } catch (err) {
        console.error('Error loading rekap:', err.message);
        req.flash('error', err.apiMessage || 'Gagal memuat rekap');
        res.redirect('/penilaian');
    }
});

module.exports = router;
