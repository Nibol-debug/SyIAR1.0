#!/bin/bash

echo "=========================================="
echo "  FIX CRUD ROLE & PERMISSION - SyIAR"
echo "=========================================="

cd /var/www/html/34/SyIAR1.0

# ============================================
# 1. FIX BACKEND - RoleController.php
# ============================================
echo ""
echo "📦 1. Membuat RoleController di Backend..."

cat > backend/app/Controllers/Api/RoleController.php << 'EOF'
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
EOF

echo "✅ RoleController.php selesai"

# ============================================
# 2. UPDATE ROUTES
# ============================================
echo ""
echo "📦 2. Update Routes..."

cat > backend/app/Config/Routes.php << 'EOF'
<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes->options('(:any)', function() {
    $response = service('response');
    $response->setStatusCode(200);
    $response->setHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
    $response->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With, Accept');
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->setHeader('Access-Control-Allow-Credentials', 'true');
    $response->setHeader('Access-Control-Max-Age', '86400');
    $response->setBody('');
    return $response;
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Auth
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('auth/me', 'AuthController::me', ['filter' => 'auth']);
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'auth']);
    
    // Roles
    $routes->group('roles', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'RoleController::index');
        $routes->get('permissions', 'RoleController::getPermissions');
        $routes->get('(:num)/permissions', 'RoleController::getRolePermissions/$1');
        $routes->post('/', 'RoleController::create');
        $routes->put('(:num)', 'RoleController::update/$1');
        $routes->delete('(:num)', 'RoleController::delete/$1');
        $routes->post('update-permission/(:num)', 'RoleController::updatePermissions/$1');
    });
});

$routes->get('/health', function() {
    return service('response')->setJSON(['status' => 'ok']);
});
EOF

echo "✅ Routes.php selesai"

# ============================================
# 3. UPDATE FRONTEND role_management.ejs
# ============================================
echo ""
echo "📦 3. Update role_management.ejs..."

cat > frontend/src/views/pages/role_management.ejs << 'EOF'
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-teal-700">Manajemen Role & Hak Akses</h2>
            <button onclick="showAddRoleModal()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-md">
                + Tambah Role Baru
            </button>
        </div>
        
        <% if (data.roles && data.roles.length > 0) { %>
            <% data.roles.forEach(role => { %>
                <div class="mb-10 p-4 border-2 border-gray-100 rounded-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-teal-600 uppercase">
                            Role: <%= role.nama_role %>
                            <span class="text-sm text-gray-400 ml-2">(<%= role.total_permissions || 0 %> permissions)</span>
                        </h3>
                        <div class="flex gap-2">
                            <button onclick="editRole('<%= role.id %>', '<%= role.nama_role %>', '<%= role.deskripsi %>')" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                Edit
                            </button>
                            <button onclick="deleteRole('<%= role.id %>', '<%= role.nama_role %>')" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                Hapus
                            </button>
                        </div>
                    </div>
                    
                    <form onsubmit="updateRole(event, '<%= role.id %>')" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <% data.all_permissions.forEach(perm => { %>
                                <div class="flex items-center p-2 border rounded hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="perms_<%= role.id %>" 
                                           value="<%= perm.id %>" 
                                           id="perm_<%= role.id %>_<%= perm.id %>"
                                           class="w-4 h-4 text-teal-600 rounded">
                                    <label for="perm_<%= role.id %>_<%= perm.id %>" class="ml-3 cursor-pointer">
                                        <span class="block text-sm font-medium text-gray-700"><%= perm.kode %></span>
                                        <span class="text-xs text-gray-400"><%= perm.deskripsi %></span>
                                    </label>
                                </div>
                            <% }) %>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm font-semibold shadow-md">
                                Simpan Akses <%= role.nama_role %>
                            </button>
                        </div>
                    </form>
                </div>
            <% }) %>
        <% } else { %>
            <p class="text-gray-500">Belum ada data role.</p>
        <% } %>
    </div>
</div>

<div id="roleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Tambah Role Baru</h3>
        <input type="hidden" id="roleId">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nama Role</label>
            <input type="text" id="roleName" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Deskripsi</label>
            <textarea id="roleDesc" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
            <button onclick="saveRole()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </div>
</div>

<script>
    const API_URL = 'http://localhost:8080/api';
    const TOKEN = '<%- token %>';
    
    console.log('Token available:', TOKEN ? 'YES' : 'NO');
    console.log('API_URL:', API_URL);
    
    async function loadRolePermissions(roleId) {
        try {
            const response = await fetch(API_URL + '/roles/' + roleId + '/permissions', {
                headers: { 'Authorization': 'Bearer ' + TOKEN }
            });
            const permIds = await response.json();
            
            permIds.forEach(function(permId) {
                var checkbox = document.querySelector('input[name="perms_' + roleId + '"][value="' + permId + '"]');
                if (checkbox) checkbox.checked = true;
            });
        } catch (error) {
            console.error('Failed to load permissions:', error);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        <% data.roles.forEach(function(role) { %>
            loadRolePermissions('<%= role.id %>');
        <% }); %>
    });
    
    async function updateRole(event, roleId) {
        event.preventDefault();
        
        var checkboxes = document.querySelectorAll('input[name="perms_' + roleId + '"]:checked');
        var selectedPerms = [];
        checkboxes.forEach(function(cb) {
            selectedPerms.push(cb.value);
        });
        
        try {
            var response = await fetch(API_URL + '/roles/update-permission/' + roleId, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + TOKEN
                },
                body: JSON.stringify({ permissions: selectedPerms })
            });
            
            var result = await response.json();
            
            if(response.ok) {
                alert('Berhasil! Hak akses role telah diperbarui.');
            } else {
                alert('Gagal: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menyambung ke server API: ' + error.message);
        }
    }
    
    function showAddRoleModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Role Baru';
        document.getElementById('roleId').value = '';
        document.getElementById('roleName').value = '';
        document.getElementById('roleDesc').value = '';
        document.getElementById('roleModal').classList.remove('hidden');
        document.getElementById('roleModal').classList.add('flex');
    }
    
    function editRole(id, name, desc) {
        document.getElementById('modalTitle').innerText = 'Edit Role';
        document.getElementById('roleId').value = id;
        document.getElementById('roleName').value = name;
        document.getElementById('roleDesc').value = desc || '';
        document.getElementById('roleModal').classList.remove('hidden');
        document.getElementById('roleModal').classList.add('flex');
    }
    
    function closeModal() {
        document.getElementById('roleModal').classList.add('hidden');
        document.getElementById('roleModal').classList.remove('flex');
    }
    
    async function saveRole() {
        var id = document.getElementById('roleId').value;
        var nama_role = document.getElementById('roleName').value;
        var deskripsi = document.getElementById('roleDesc').value;
        
        if (!nama_role) {
            alert('Nama role wajib diisi');
            return;
        }
        
        var url = id ? API_URL + '/roles/' + id : API_URL + '/roles';
        var method = id ? 'PUT' : 'POST';
        
        console.log('Saving role:', { url: url, method: method, nama_role: nama_role, deskripsi: deskripsi });
        
        try {
            var response = await fetch(url, {
                method: method,
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + TOKEN
                },
                body: JSON.stringify({ nama_role: nama_role, deskripsi: deskripsi })
            });
            
            var result = await response.json();
            console.log('Response:', result);
            
            if(response.ok) {
                alert(result.message);
                location.reload();
            } else {
                alert('Gagal: ' + JSON.stringify(result));
            }
        } catch (error) {
            console.error('Error saving role:', error);
            alert('Gagal menyambung ke server API: ' + error.message);
        }
    }
    
    async function deleteRole(id, name) {
        if(confirm('Hapus role "' + name + '"?')) {
            try {
                var response = await fetch(API_URL + '/roles/' + id, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + TOKEN }
                });
                
                var result = await response.json();
                
                if(response.ok) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Gagal: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error deleting role:', error);
                alert('Gagal menyambung ke server API: ' + error.message);
            }
        }
    }
</script>
EOF

echo "✅ role_management.ejs selesai"

# ============================================
# 4. UPDATE server.js
# ============================================
echo ""
echo "📦 4. Update server.js..."

cat > frontend/server.js << 'EOF'
require('dotenv').config();
const express = require('express');
const session = require('express-session');
const cookieParser = require('cookie-parser');
const path = require('path');
const axios = require('axios');
const expressLayouts = require('express-ejs-layouts');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'src/public')));

// Layout
app.use(expressLayouts);
app.set('layout', 'layouts/main');
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'src/views'));

// Session
app.use(session({
    secret: process.env.SESSION_SECRET || 'default-secret',
    resave: false,
    saveUninitialized: false,
    cookie: {
        secure: false,
        httpOnly: true,
        maxAge: 24 * 60 * 60 * 1000
    }
}));

// Global variables
app.use((req, res, next) => {
    res.locals.user = req.session.user || null;
    res.locals.currentPath = req.path;
    next();
});

// Routes
app.get('/', (req, res) => {
    if (req.session.user) {
        res.redirect('/dashboard');
    } else {
        res.redirect('/login');
    }
});

app.get('/login', (req, res) => {
    if (req.session.user) {
        return res.redirect('/dashboard');
    }
    res.render('pages/login', { title: 'Login', error: null });
});

app.post('/login', async (req, res) => {
    try {
        const response = await axios.post('http://localhost:8080/api/auth/login', {
            username: req.body.username,
            password: req.body.password
        });
        
        if (response.data.status === 'success') {
            req.session.token = response.data.data.token;
            req.session.user = response.data.data.user;
            req.session.permissions = response.data.data.permissions || [];
            res.redirect('/dashboard');
        } else {
            res.render('pages/login', { 
                title: 'Login', 
                error: response.data.message || 'Login gagal'
            });
        }
    } catch (error) {
        let errorMsg = 'Login gagal, silakan coba lagi';
        if (error.code === 'ECONNREFUSED') {
            errorMsg = 'Tidak dapat terhubung ke server. Pastikan backend berjalan di port 8080.';
        } else if (error.response) {
            errorMsg = error.response.data?.message || errorMsg;
        }
        res.render('pages/login', { title: 'Login', error: errorMsg });
    }
});

app.get('/dashboard', async (req, res) => {
    if (!req.session.token) {
        return res.redirect('/login');
    }
    
    try {
        const response = await axios.get('http://localhost:8080/api/auth/me', {
            headers: { Authorization: `Bearer ${req.session.token}` }
        });
        
        res.render('pages/dashboard', { 
            title: 'Dashboard',
            user: response.data.data.user
        });
    } catch (error) {
        req.session.destroy();
        res.redirect('/login');
    }
});

app.get('/admin/roles', async (req, res) => {
    if (!req.session.token) {
        return res.redirect('/login');
    }
    
    try {
        const [rolesRes, permsRes] = await Promise.all([
            axios.get('http://localhost:8080/api/roles', {
                headers: { Authorization: `Bearer ${req.session.token}` }
            }),
            axios.get('http://localhost:8080/api/roles/permissions', {
                headers: { Authorization: `Bearer ${req.session.token}` }
            })
        ]);
        
        res.render('pages/role_management', {
            title: 'Manajemen Role',
            data: {
                roles: rolesRes.data,
                all_permissions: permsRes.data
            },
            token: req.session.token
        });
    } catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Gagal mengambil data role. Pastikan backend berjalan.');
    }
});

app.get('/logout', (req, res) => {
    req.session.destroy();
    res.redirect('/login');
});

app.listen(PORT, () => {
    console.log('✅ Frontend running on http://localhost:' + PORT);
    console.log('📡 API connected to http://localhost:8080/api');
});
EOF

echo "✅ server.js selesai"

# ============================================
# 5. RESTART SERVER
# ============================================
echo ""
echo "📦 5. Restart Server..."

pkill -f "php spark serve" 2>/dev/null
pkill -f "node server.js" 2>/dev/null

cd /var/www/html/34/SyIAR1.0/backend
php spark serve --port 8080 > /dev/null 2>&1 &

cd /var/www/html/34/SyIAR1.0/frontend
npm run dev > /dev/null 2>&1 &

sleep 3

# ============================================
# 6. TEST API
# ============================================
echo ""
echo "📦 6. Testing API..."

LOGIN=$(curl -s -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"superadmin","password":"Admin123!"}')

TOKEN=$(echo $LOGIN | php -r 'echo json_decode(file_get_contents("php://stdin"))->data->token;' 2>/dev/null)

if [ -n "$TOKEN" ]; then
    echo "✅ Login berhasil, token didapat"
    
    echo "Testing GET /api/roles..."
    curl -s -X GET http://localhost:8080/api/roles \
      -H "Authorization: Bearer $TOKEN" | head -50
else
    echo "❌ Login gagal, cek backend"
fi

echo ""
echo "=========================================="
echo "  ✅ FIX COMPLETE!"
echo "=========================================="
echo ""
echo "Akses: http://localhost:3000/admin/roles"
echo "Login: superadmin / Admin123!"
