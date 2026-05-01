<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($userData)
{
    $issuedAtTime = time();
    $expireTime = $issuedAtTime + getenv('JWT_ACCESS_TOKEN_EXPIRE');
    
    $payload = [
        'iat' => $issuedAtTime,
        'exp' => $expireTime,
        'data' => [
            'id' => $userData['id'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'roles' => $userData['roles'] ?? []
        ]
    ];
    
    return JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');
}

function validateJWT($token)
{
    try {
        $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}

function getUserPermissions($userId)
{
    $db = \Config\Database::connect();
    
    $permissions = $db->table('permissions')
        ->select('permissions.kode')
        ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
        ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
        ->where('user_roles.user_id', $userId)
        ->get()
        ->getResultArray();
    
    return array_column($permissions, 'kode');
}