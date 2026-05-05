<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * SyIAR Gemilang API Routes
 * Phase 1: Auth + JWT + CORS
 * Phase 2: RBAC (Roles & Permissions)
 * Phase 3: Santri, Kelas, Penilaian, PPDB
 */

// CORS Preflight handler
$routes->options('(:any)', function() {
    $response = service('response');
    $response->setStatusCode(200);
    
    $origin = service('request')->getHeaderLine('Origin');
    $allowedOrigins = ['http://localhost:3000', 'http://localhost:3001'];
    
    if (in_array($origin, $allowedOrigins)) {
        $response->setHeader('Access-Control-Allow-Origin', $origin);
    }
    
    $response->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With, Accept');
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->setHeader('Access-Control-Allow-Credentials', 'true');
    $response->setHeader('Access-Control-Max-Age', '86400');
    $response->setBody('');
    return $response;
});

// Health check
$routes->get('/health', function() {
    return service('response')->setJSON([
        'status'    => 'ok',
        'timestamp' => date('Y-m-d H:i:s'),
        'app'       => 'SyIAR Gemilang API'
    ]);
});

// ============ ALL API ROUTES ============
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // ---- Phase 1: Authentication ----
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('auth/me', 'AuthController::me', ['filter' => 'auth']);
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'auth']);
    
    // ---- Phase 2: RBAC ----
    $routes->group('roles', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'RoleController::index');
        $routes->get('permissions', 'RoleController::getPermissions');
        $routes->get('(:num)/permissions', 'RoleController::getRolePermissions/$1');
        $routes->post('/', 'RoleController::create');
        $routes->put('(:num)', 'RoleController::update/$1');
        $routes->delete('(:num)', 'RoleController::delete/$1');
        $routes->post('update-permission/(:num)', 'RoleController::updatePermissions/$1');
    });
    
    // ---- Phase 3: Santri Management ----
    $routes->resource('santri', ['controller' => 'SantriController', 'filter' => 'auth']);
    $routes->post('santri/import', 'SantriController::import', ['filter' => 'auth']);
    $routes->get('santri/export', 'SantriController::export', ['filter' => 'auth']);
    
    // ---- Phase 3: Kelas Management ----
    $routes->resource('kelas', ['controller' => 'KelasController', 'filter' => 'auth']);
    
    // ---- Phase 3: Penilaian System ----
    $routes->get('penilaian/aspek/(:num)', 'PenilaianController::getAspekByKategori/$1', ['filter' => 'auth']);
    $routes->post('penilaian/submit', 'PenilaianController::submit', ['filter' => 'auth']);
    $routes->get('penilaian/rekap', 'PenilaianController::rekap', ['filter' => 'auth']);
    $routes->get('penilaian/export/excel', 'PenilaianController::exportExcel', ['filter' => 'auth']);
    
    // ---- Phase 3: Kategori & Aspek Penilaian ----
    $routes->resource('kategori-penilaian', ['controller' => 'KategoriPenilaianController', 'filter' => 'auth']);
    $routes->resource('aspek-penilaian', ['controller' => 'AspekPenilaianController', 'filter' => 'auth']);
    
    // ---- Phase 3: PPDB Online ----
    $routes->resource('ppdb', ['controller' => 'PpdbController', 'filter' => 'auth']);
    $routes->get('ppdb/stats', 'PpdbController::stats', ['filter' => 'auth']);
    $routes->put('ppdb/status/(:num)', 'PpdbController::updateStatus/$1', ['filter' => 'auth']);
    
    // ---- Dashboard Stats ----
    $routes->get('dashboard/stats', 'DashboardController::stats', ['filter' => 'auth']);
});

