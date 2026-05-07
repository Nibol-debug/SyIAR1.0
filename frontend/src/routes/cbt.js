const express = require('express');
const router = express.Router();
const { requireAuth } = require('../middleware/auth');
const permission = require('../middleware/permission');

const { createApiClient } = require('../services/apiClient');

// Render views
router.get('/bank-soal', requireAuth, async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const [bankRes, mapelRes] = await Promise.all([
            client.get('/bank-soal'),
            client.get('/mata-pelajaran')
        ]);
        res.render('pages/cbt/bank_soal', { 
            title: 'Bank Soal',
            soal: bankRes.data?.data || [],
            mapel: mapelRes.data?.data || [],
            token: req.session.token || ''
        });
    } catch (err) {
        req.flash('error', 'Gagal memuat bank soal');
        res.redirect('/dashboard');
    }
});

router.get('/ujian-manage', requireAuth, async (req, res) => {
    try {
        const client = createApiClient(req.session.token);
        const [ujianRes, mapelRes, kelasRes, soalRes] = await Promise.all([
            client.get('/ujian'),
            client.get('/mata-pelajaran'),
            client.get('/kelas'),
            client.get('/bank-soal')
        ]);
        res.render('pages/cbt/ujian_manage', { 
            title: 'Manajemen Ujian',
            ujian: ujianRes.data?.data || [],
            mapel: mapelRes.data?.data || [],
            kelas: kelasRes.data?.data || [],
            allSoal: soalRes.data?.data || [],
            token: req.session.token || ''
        });
    } catch (err) {
        req.flash('error', 'Gagal memuat manajemen ujian');
        res.redirect('/dashboard');
    }
});

router.get('/ujian-monitor', requireAuth, async (req, res) => {
    try {
        const { ujian_id } = req.query;
        if (!ujian_id) {
            req.flash('error', 'Pilih ujian yang ingin dimonitor');
            return res.redirect('/cbt/ujian-manage');
        }
        const client = createApiClient(req.session.token);
        const [monitorRes, ujianRes] = await Promise.all([
            client.get(`/ujian/${ujian_id}/monitor`),
            client.get(`/ujian/${ujian_id}`)
        ]);
        res.render('pages/cbt/ujian_monitor', { 
            title: 'Monitor Ujian',
            peserta: monitorRes.data?.data || [],
            ujian: ujianRes.data?.data || {}
        });
    } catch (err) {
        req.flash('error', 'Gagal memuat data monitor');
        res.redirect('/cbt/ujian-manage');
    }
});

router.get('/ujian-siswa', requireAuth, (req, res) => {
    // If sesi_id is in query, render the exam interface
    if (req.query.sesi_id) {
        return res.render('pages/cbt/ujian_siswa', { 
            title: 'Ujian Online',
            token: req.session.token
        });
    }
    // Otherwise render the entry page
    res.render('pages/cbt/ujian_masuk', { 
        title: 'Masuk Ujian',
        token: req.session.token
    });
});

router.get('/analisis-soal', requireAuth, async (req, res) => {
    try {
        const { ujian_id } = req.query;
        const client = createApiClient(req.session.token);
        const ujianRes = await client.get('/ujian');
        
        let analisis = [];
        if (ujian_id) {
            const analisisRes = await client.get(`/penilaian/analisis-soal/${ujian_id}`);
            analisis = analisisRes.data?.data || [];
        }
        
        res.render('pages/cbt/analisis_soal', { 
            title: 'Analisis Butir Soal',
            ujian: ujianRes.data?.data || [],
            analisis,
            selectedUjianId: ujian_id
        });
    } catch (err) {
        req.flash('error', 'Gagal memuat analisis');
        res.redirect('/dashboard');
    }
});

module.exports = router;
