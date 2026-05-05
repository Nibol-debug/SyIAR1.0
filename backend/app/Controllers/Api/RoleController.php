<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class RoleController extends BaseController
{
    use ResponseTrait;
    
    private $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    // GET /api/roles
    public function index()
    {
        $roles = $this->db->table('roles')
            ->select('roles.*, COUNT(role_permissions.permission_id) as total_permissions')
            ->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
            ->groupBy('roles.id')
            ->orderBy('roles.id', 'ASC')
            ->get()
            ->getResultArray();
        
        return $this->respond([
            'success' => true,
            'data'    => $roles
        ]);
    }
    
    // GET /api/roles/permissions
    public function getPermissions()
    {
        $permissions = $this->db->table('permissions')
            ->orderBy('module', 'ASC')
            ->orderBy('action', 'ASC')
            ->get()
            ->getResultArray();
        
        return $this->respond([
            'success' => true,
            'data'    => $permissions
        ]);
    }
    
    // GET /api/roles/{id}/permissions
    public function getRolePermissions($roleId)
    {
        $permissions = $this->db->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();
        
        $permIds = array_column($permissions, 'permission_id');
        
        return $this->respond([
            'success' => true,
            'data'    => $permIds
        ]);
    }
    
    // POST /api/roles
    public function create()
    {
        $json = $this->request->getJSON(true);
        $name = $json['name'] ?? $this->request->getVar('name');
        $description = $json['description'] ?? $this->request->getVar('description');
        
        if (!$name) {
            return $this->fail('Nama role wajib diisi', 400);
        }
        
        // Generate slug from name
        $slug = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $name)));
        
        $exists = $this->db->table('roles')
            ->where('slug', $slug)
            ->get()
            ->getRow();
        
        if ($exists) {
            return $this->fail('Role "' . $name . '" sudah ada', 400);
        }
        
        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('roles')->insert($data);
        $newId = $this->db->insertID();
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Role berhasil ditambahkan',
            'data'    => ['id' => $newId, 'role' => $data]
        ]);
    }
    
    // PUT /api/roles/{id}
    public function update($id)
    {
        $json = $this->request->getJSON(true);
        $name = $json['name'] ?? $this->request->getVar('name');
        $description = $json['description'] ?? $this->request->getVar('description');
        
        if (!$name) {
            return $this->fail('Nama role wajib diisi', 400);
        }
        
        $slug = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $name)));
        
        $exists = $this->db->table('roles')
            ->where('slug', $slug)
            ->where('id !=', $id)
            ->get()
            ->getRow();
        
        if ($exists) {
            return $this->fail('Role "' . $name . '" sudah digunakan oleh role lain', 400);
        }
        
        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('roles')->where('id', $id)->update($data);
        
        return $this->respond([
            'success' => true,
            'message' => 'Role berhasil diupdate'
        ]);
    }
    
    // DELETE /api/roles/{id}
    public function delete($id)
    {
        $role = $this->db->table('roles')->where('id', $id)->get()->getRow();
        
        if (!$role) {
            return $this->failNotFound('Role tidak ditemukan');
        }
        
        if ($role->slug === 'super_admin') {
            return $this->fail('Role super_admin tidak bisa dihapus', 400);
        }
        
        $userCount = $this->db->table('user_roles')
            ->where('role_id', $id)
            ->countAllResults();
        
        if ($userCount > 0) {
            return $this->fail('Role tidak bisa dihapus karena masih dimiliki oleh ' . $userCount . ' user', 400);
        }
        
        $this->db->table('role_permissions')->where('role_id', $id)->delete();
        $this->db->table('roles')->where('id', $id)->delete();
        
        return $this->respond([
            'success' => true,
            'message' => 'Role berhasil dihapus'
        ]);
    }
    
    // POST /api/roles/update-permission/{id}
    public function updatePermissions($roleId)
    {
        $data = $this->request->getJSON(true);
        $permissions = $data['permissions'] ?? [];
        
        $this->db->table('role_permissions')->where('role_id', $roleId)->delete();
        
        foreach ($permissions as $permId) {
            $this->db->table('role_permissions')->insert([
                'role_id'       => $roleId,
                'permission_id' => $permId
            ]);
        }
        
        return $this->respond([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }
}
