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
        // Skip jika tidak ada koneksi atau error
        try {
            // Pastikan user_id valid (jika 0 atau null, set ke null)
            $validUserId = ($userId > 0) ? $userId : null;
            
            $data = [
                'user_id' => $validUserId,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $request->getUserAgent()->getAgentString() ?: 'Unknown',
                'login_time' => date('Y-m-d H:i:s'),
                'status' => $status
            ];
            
            // Coba insert
            $result = $this->insert($data);
            
            // Jika gagal karena foreign key, insert tanpa user_id
            if (!$result && $this->db->error()['code'] == 1452) {
                unset($data['user_id']);
                $result = $this->insert($data);
            }
            
            return $result;
        } catch (\Exception $e) {
            // Log error tapi jangan throw
            log_message('error', 'Login log failed (non-critical): ' . $e->getMessage());
            return false;
        }
    }
}
