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
        
        // Debug: Log incoming request
        log_message('debug', 'Login attempt received');
        
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 400);
        }
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        // Cari user
        $user = $this->userModel->where('username', $username)
                                ->orWhere('email', $username)
                                ->first();
        
        if (!$user || !password_verify($password, $user->password_hash)) {
            // Log failed attempt (user_id = 0 atau null)
            $this->loginLogModel->logAttempt(0, 'failed', $this->request);
            
            return $this->fail([
                'status' => 'error',
                'message' => 'Username atau password salah'
            ], 401);
        }
        
        if (!$user->is_active) {
            return $this->fail([
                'status' => 'error',
                'message' => 'Akun Anda telah dinonaktifkan'
            ], 403);
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
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => $userData,
                'permissions' => $permissions
            ]
        ]);
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