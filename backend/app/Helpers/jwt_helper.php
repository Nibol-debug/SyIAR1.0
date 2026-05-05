<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($userData)
{
    $issuedAtTime = time();
    $expireTime = $issuedAtTime + (int) env('JWT_ACCESS_TOKEN_EXPIRE', 7200);
    
    $payload = [
        'iat' => $issuedAtTime,
        'exp' => $expireTime,
        'iss' => 'SyIAR_Gemilang',
        'data' => [
            'id'       => $userData['id'],
            'username' => $userData['username'],
            'email'    => $userData['email'],
            'roles'    => $userData['roles'] ?? []
        ]
    ];
    
    return JWT::encode($payload, env('JWT_SECRET_KEY', 'SyIAR_S3cr3t_K3y_2026'), 'HS256');
}

function validateJWT($token)
{
    try {
        $decoded = JWT::decode($token, new Key(env('JWT_SECRET_KEY', 'SyIAR_S3cr3t_K3y_2026'), 'HS256'));
        return json_decode(json_encode($decoded), true);
    } catch (Exception $e) {
        log_message('warning', 'JWT validation failed: ' . $e->getMessage());
        return null;
    }
}

function getUserPermissions($userId)
{
    $db = \Config\Database::connect();
    
    $permissions = $db->table('permissions')
        ->select('permissions.code')
        ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
        ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
        ->where('user_roles.user_id', $userId)
        ->get()
        ->getResultArray();
    
    return array_unique(array_column($permissions, 'code'));
}