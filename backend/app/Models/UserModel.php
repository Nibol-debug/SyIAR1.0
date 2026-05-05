<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = ['username', 'email', 'password', 'nama_lengkap', 'phone', 'avatar', 'is_active', 'last_login'];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'username' => 'required|min_length[3]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'nama_lengkap' => 'required|min_length[3]',
    ];
    
    public function getUserWithRoles($userId = null)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, GROUP_CONCAT(roles.name) as roles');
        $builder->join('user_roles', 'user_roles.user_id = users.id', 'left');
        $builder->join('roles', 'roles.id = user_roles.role_id', 'left');
        
        if ($userId) {
            $builder->where('users.id', $userId);
            return $builder->get()->getRow();
        }
        
        $builder->groupBy('users.id');
        return $builder->get()->getResult();
    }
}