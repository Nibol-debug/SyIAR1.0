<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 🔥 IMPORTANT: Handle OPTIONS preflight untuk SEMUA route
$routes->options('(:any)', function() {
    $response = service('response');
    $response->setStatusCode(200);
    $response->setHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
    $response->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With, Accept');
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->setHeader('Access-Control-Allow-Credentials', 'true');
    $response->setHeader('Access-Control-Max-Age', '86400');
    $response->setBody('');
    return $response;
});

// API Routes Group
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Public routes
    $routes->post('auth/login', 'AuthController::login');
    
    // Protected routes (akan diaktifkan nanti)
    $routes->group('', ['filter' => 'auth'], function($routes) {
        $routes->get('auth/me', 'AuthController::me');
        $routes->post('auth/logout', 'AuthController::logout');
    });
});

// Health check
$routes->get('/health', function() {
    return service('response')->setJSON(['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')]);
});

// Default route (optional)
$routes->get('/', function() {
    return 'SyIAR API is running';
});