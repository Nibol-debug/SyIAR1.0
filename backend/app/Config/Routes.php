<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

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

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Auth
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('auth/me', 'AuthController::me', ['filter' => 'auth']);
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'auth']);
    
    // Roles
    $routes->group('roles', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'RoleController::index');
        $routes->get('permissions', 'RoleController::getPermissions');
        $routes->get('(:num)/permissions', 'RoleController::getRolePermissions/$1');
        $routes->post('/', 'RoleController::create');
        $routes->put('(:num)', 'RoleController::update/$1');
        $routes->delete('(:num)', 'RoleController::delete/$1');
        $routes->post('update-permission/(:num)', 'RoleController::updatePermissions/$1');
    });
});

$routes->get('/health', function() {
    return service('response')->setJSON(['status' => 'ok']);
});
