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
            ->getResult();
        
        return $this->respond($roles);
    }
    
    // GET /api/roles/permissions
    public function getPermissions()
    {
        $permissions = $this->db->table('permissions')
            ->orderBy('modul', 'ASC')
            ->get()
            ->getResult();
        
        return $this->respond($permissions);
    }
    
    // GET /api/roles/{id}/permissions
    public function getRolePermissions($roleId)
    {
        $permissions = $this->db->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->get()
            ->getResult();
        
        $permIds = array_column($permissions, 'permission_id');
        
        return $this->respond($permIds);
    }
    
    // POST /api/roles
    public function create()
    {
        $nama_role = $this->request->getVar('nama_role');
        $deskripsi = $this->request->getVar('deskripsi');
        
        if (!$nama_role) {
            return $this->fail('Nama role wajib diisi', 400);
        }
        
        $exists = $this->db->table('roles')
            ->where('nama_role', $nama_role)
            ->get()
            ->getRow();
        
        if ($exists) {
            return $this->fail('Role "' . $nama_role . '" sudah ada', 400);
        }
        
        $data = [
            'nama_role' => $nama_role,
            'deskripsi' => $deskripsi,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('roles')->insert($data);
        $newId = $this->db->insertID();
        
        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Role berhasil ditambahkan',
            'id' => $newId,
            'role' => $data
        ]);
    }
    
    // PUT /api/roles/{id}
    public function update($id)
    {
        $nama_role = $this->request->getVar('nama_role');
        $deskripsi = $this->request->getVar('deskripsi');
        
        if (!$nama_role) {
            return $this->fail('Nama role wajib diisi', 400);
        }
        
        $exists = $this->db->table('roles')
            ->where('nama_role', $nama_role)
            ->where('id !=', $id)
            ->get()
            ->getRow();
        
        if ($exists) {
            return $this->fail('Role "' . $nama_role . '" sudah digunakan oleh role lain', 400);
        }
        
        $data = [
            'nama_role' => $nama_role,
            'deskripsi' => $deskripsi,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('roles')->where('id', $id)->update($data);
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Role berhasil diupdate'
        ]);
    }
    
    // DELETE /api/roles/{id}
    public function delete($id)
    {
        $role = $this->db->table('roles')->where('id', $id)->get()->getRow();
        
        if ($role && $role->nama_role === 'super_admin') {
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
            'status' => 'success',
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
                'role_id' => $roleId,
                'permission_id' => $permId
            ]);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Permissions updated successfully'
        ]);
    }
}
