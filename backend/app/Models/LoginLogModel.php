<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginLogModel extends Model
{
    protected $table = 'login_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'ip_address', 'user_agent', 'login_time', 'status'];
    protected $useTimestamps = false;
    protected $skipValidation = true;
    
    public function logAttempt($userId, $status, $request)
    {
        try {
            // Pastikan user_id valid (jika 0, set ke null)
            $validUserId = ($userId > 0) ? $userId : null;
            
            $data = [
                'user_id' => $validUserId,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $request->getUserAgent()->getAgentString() ?: 'Unknown',
                'login_time' => date('Y-m-d H:i:s'),
                'status' => $status
            ];
            
            return $this->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Login log failed (non-critical): ' . $e->getMessage());
            return false;
        }
    }
}