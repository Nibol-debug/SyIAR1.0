const axios = require('axios');

const API_BASE_URL = process.env.API_BASE_URL || 'http://localhost:8081/api';
const API_TIMEOUT = parseInt(process.env.API_TIMEOUT) || 15000;

const createApiClient = (token = null) => {
    const instance = axios.create({
        baseURL: API_BASE_URL,
        timeout: API_TIMEOUT,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    });
    
    instance.interceptors.request.use((config) => {
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    });
    
    instance.interceptors.response.use(
        (res) => res,
        (error) => {
            error.isApiError = true;
            error.apiStatus = error.response?.status;
            error.apiMessage = error.response?.data?.messages?.error 
                || error.response?.data?.message 
                || error.response?.data?.error 
                || 'API error';
            return Promise.reject(error);
        }
    );
    return instance;
};

// Phase 1-3 API Helpers
const api = {
    // Santri
    santri: {
        list: (params = {}, token = null) => createApiClient(token).get('/santri', { params }),
        get: (id, token = null) => createApiClient(token).get(`/santri/${id}`),
        create: (data, token = null) => createApiClient(token).post('/santri', data),
        update: (id, data, token = null) => createApiClient(token).put(`/santri/${id}`, data),
        delete: (id, token = null) => createApiClient(token).delete(`/santri/${id}`),
        import: (formData, token = null) => createApiClient(token).post('/santri/import', formData, { headers: { 'Content-Type': 'multipart/form-data' }}),
        export: (token = null) => createApiClient(token).get('/santri/export', { responseType: 'blob' }),
    },
    // Kelas
    kelas: {
        list: (params = {}, token = null) => createApiClient(token).get('/kelas', { params }),
        create: (data, token = null) => createApiClient(token).post('/kelas', data),
        update: (id, data, token = null) => createApiClient(token).put(`/kelas/${id}`, data),
        delete: (id, token = null) => createApiClient(token).delete(`/kelas/${id}`),
    },
    // Penilaian
    penilaian: {
        getAspek: (kategoriId, token = null) => createApiClient(token).get(`/penilaian/aspek/${kategoriId}`),
        submit: (data, token = null) => createApiClient(token).post('/penilaian/submit', data),
        rekap: (params = {}, token = null) => createApiClient(token).get('/penilaian/rekap', { params }),
        export: (params = {}, token = null) => createApiClient(token).get('/penilaian/export/excel', { params, responseType: 'blob' }),
    },
    // PPDB
    ppdb: {
        list: (params = {}, token = null) => createApiClient(token).get('/ppdb', { params }),
        stats: (token = null) => createApiClient(token).get('/ppdb/stats'),
        create: (formData, token = null) => createApiClient(token).post('/ppdb', formData, { headers: { 'Content-Type': 'multipart/form-data' }}),
        updateStatus: (id, data, token = null) => createApiClient(token).put(`/ppdb/status/${id}`, data),
    },
    // Generic methods (accept token as last param)
    get: (url, params = {}, token = null) => createApiClient(token).get(url, { params }),
    post: (url, data = {}, token = null) => createApiClient(token).post(url, data),
    put: (url, data = {}, token = null) => createApiClient(token).put(url, data),
    delete: (url, token = null) => createApiClient(token).delete(url),
};

module.exports = { createApiClient, api };
