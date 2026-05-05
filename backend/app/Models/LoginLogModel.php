<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginLogModel extends Model
{
    protected $table = 'login_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'username', 'ip_address', 'user_agent',
        'login_status', 'failure_reason', 'login_time', 'logout_time'
    ];
    protected $useTimestamps = false;
    protected $skipValidation = true;
    
    public function logAttempt($userId, $status, $request, $username = null, $reason = null)
    {
        try {
            $validUserId = ($userId > 0) ? $userId : null;
            
            $data = [
                'user_id'        => $validUserId,
                'username'       => $username ?? 'unknown',
                'ip_address'     => $request->getIPAddress(),
                'user_agent'     => (string) $request->getUserAgent(),
                'login_status'   => $status,
                'failure_reason' => $reason,
                'login_time'     => date('Y-m-d H:i:s'),
            ];
            
            return $this->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Login log failed (non-critical): ' . $e->getMessage());
            return false;
        }
    }
}