const express = require('express');
const router = express.Router();
const { api } = require('../services/apiClient');

router.use((req, res, next) => {
    if (!req.session.user) return res.redirect('/login');
    next();
});

router.get('/', async (req, res) => {
    try {
        const { page = 1, limit = 20 } = req.query;
        const response = await api.get('/alumni', { page, limit }, req.session.token);
        
        res.render('alumni/index', {
            title: 'Data Alumni - SyIAR Gemilang',
            alumni: response.data.data?.data || [],
            pagination: response.data.data?.pagination || {},
        });
    } catch (error) {
        res.render('alumni/index', {
            title: 'Data Alumni - SyIAR Gemilang',
            alumni: [],
            pagination: {},
        });
    }
});

module.exports = router;
