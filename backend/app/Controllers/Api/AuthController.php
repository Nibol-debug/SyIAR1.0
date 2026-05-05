<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LoginLogModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $loginLogModel;
    protected $jwtSecret;
    protected $jwtExpire;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loginLogModel = new LoginLogModel();
        $this->jwtSecret = env('JWT_SECRET_KEY', 'SyIAR_S3cr3t_K3y_2026_!@#$%^&*');
        $this->jwtExpire = (int) env('JWT_ACCESS_TOKEN_EXPIRE', 7200);
    }

    public function login()
    {
        try {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            // Cari user berdasarkan username
            $user = $this->userModel->where('username', $username)->first();

            if (!$user) {
                // Log failed login
                $this->logLogin($username, 'failed', 'User tidak ditemukan');
                return $this->fail('Username atau password salah', 401);
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                // Log failed login
                $this->logLogin($username, 'failed', 'Password salah', $user['id']);
                return $this->fail('Username atau password salah', 401);
            }

            // Check if user active
            if (!$user['is_active']) {
                $this->logLogin($username, 'failed', 'User tidak aktif', $user['id']);
                return $this->fail('Akun Anda tidak aktif. Hubungi administrator.', 403);
            }

            // Get user roles & permissions
            $userRoles = $this->getUserRoles($user['id']);
            $permissions = $this->getUserPermissions($user['id']);

            // Determine primary role slug for frontend middleware
            $primaryRole = !empty($userRoles) ? $userRoles[0]['slug'] : 'user';

            // Generate JWT Token
            $issuedAt = time();
            $payload = [
                'iat' => $issuedAt,
                'exp' => $issuedAt + $this->jwtExpire,
                'iss' => 'SyIAR_Gemilang',
                'sub' => $user['id'],
                'data' => [
                    'id'            => $user['id'],
                    'username'      => $user['username'],
                    'email'         => $user['email'],
                    'nama_lengkap'  => $user['nama_lengkap'],
                    'roles'         => $userRoles
                ]
            ];

            $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

            // Update last login
            $this->userModel->update($user['id'], [
                'last_login' => date('Y-m-d H:i:s')
            ]);

            // Log successful login
            $this->logLogin($username, 'success', null, $user['id']);

            return $this->respond([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id'            => $user['id'],
                        'username'      => $user['username'],
                        'email'         => $user['email'],
                        'nama_lengkap'  => $user['nama_lengkap'],
                        'role'          => $primaryRole,
                        'roles'         => $userRoles,
                        'permissions'   => $permissions
                    ],
                    'permissions' => $permissions
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return $this->failServerError('Terjadi kesalahan pada server: ' . $e->getMessage());
        }
    }

    public function me()
    {
        try {
            $token = $this->request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $token);

            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            
            $user = $this->userModel->find($decoded->sub);
            
            if (!$user) {
                return $this->fail('User not found', 404);
            }

            $userRoles = $this->getUserRoles($user['id']);
            $primaryRole = !empty($userRoles) ? $userRoles[0]['slug'] : 'user';

            return $this->respond([
                'success' => true,
                'data' => [
                    'id'            => $user['id'],
                    'username'      => $user['username'],
                    'email'         => $user['email'],
                    'nama_lengkap'  => $user['nama_lengkap'],
                    'role'          => $primaryRole,
                    'roles'         => $userRoles,
                    'permissions'   => $this->getUserPermissions($user['id'])
                ]
            ]);

        } catch (\Exception $e) {
            return $this->fail('Invalid token', 401);
        }
    }

    public function logout()
    {
        // Untuk JWT, logout di-handle di frontend dengan menghapus token
        return $this->respond([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    private function getUserRoles($userId)
    {
        $db = \Config\Database::connect();
        $query = $db->table('user_roles ur')
            ->select('r.name, r.slug')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->get();
        
        return $query->getResultArray();
    }

    private function getUserPermissions($userId)
    {
        $db = \Config\Database::connect();
        $query = $db->table('user_roles ur')
            ->select('p.code')
            ->join('role_permissions rp', 'rp.role_id = ur.role_id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('ur.user_id', $userId)
            ->get();
        
        $result = $query->getResultArray();
        return array_unique(array_column($result, 'code'));
    }

    private function logLogin($username, $status, $reason = null, $userId = null)
    {
        $this->loginLogModel->insert([
            'user_id'        => $userId,
            'username'       => $username,
            'ip_address'     => $this->request->getIPAddress(),
            'user_agent'     => (string) $this->request->getUserAgent(),
            'login_status'   => $status,
            'failure_reason' => $reason,
            'login_time'     => date('Y-m-d H:i:s')
        ]);
    }
}
