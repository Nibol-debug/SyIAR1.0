<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LoginLogModel;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;
    
    protected $userModel;
    protected $loginLogModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loginLogModel = new LoginLogModel();
    }
    
    public function login()
    {
        helper('jwt_helper');
        
        // Debug log
        log_message('debug', 'Login attempt received: ' . json_encode($this->request->getJSON(true)));
        
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail([
                'status' => 'error',
                'message' => 'Username dan password wajib diisi'
            ], 400);
        }
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        // Cari user
        $user = $this->userModel->where('username', $username)
                                ->orWhere('email', $username)
                                ->first();
        
        // 🔥 FIX: Error spesifik untuk username tidak ditemukan
        if (!$user) {
            // Log failed attempt
            $this->loginLogModel->logAttempt(0, 'failed', $this->request);
            
            return $this->fail([
                'status' => 'error',
                'message' => 'Username tidak ditemukan'
            ], 401);  // ← 401 Unauthorized, bukan 500
        }
        
        // 🔥 FIX: Error spesifik untuk password salah
        if (!password_verify($password, $user->password_hash)) {
            // Log failed attempt
            $this->loginLogModel->logAttempt($user->id, 'failed', $this->request);
            
            return $this->fail([
                'status' => 'error',
                'message' => 'Password salah'
            ], 401);  // ← 401 Unauthorized
        }
        
        // Cek status akun
        if (!$user->is_active) {
            return $this->fail([
                'status' => 'error',
                'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.'
            ], 403);  // ← 403 Forbidden
        }
        
        // Get user roles
        $userWithRoles = $this->userModel->getUserWithRoles($user->id);
        $userRoles = [];
        
        if ($userWithRoles && isset($userWithRoles->roles)) {
            $userRoles = explode(',', $userWithRoles->roles);
        }
        
        // Get permissions
        $permissions = getUserPermissions($user->id);
        
        // Generate JWT
        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'nama_lengkap' => $user->nama_lengkap,
            'roles' => $userRoles
        ];
        
        $token = generateJWT($userData);
        
        // Log success
        $this->loginLogModel->logAttempt($user->id, 'success', $this->request);
        
        // 🔥 SUCCESS: Return 200 OK
        return $this->respond([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => $userData,
                'permissions' => $permissions
            ]
        ], 200);
    }
    
    public function me()
    {
        $user = $this->request->user ?? null;
        
        if (!$user) {
            return $this->failUnauthorized('Token tidak valid');
        }
        
        $permissions = getUserPermissions($user->id);
        
        return $this->respond([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'permissions' => $permissions
            ]
        ]);
    }
    
    public function logout()
    {
        return $this->respond([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}