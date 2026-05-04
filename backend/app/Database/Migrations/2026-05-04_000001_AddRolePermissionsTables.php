<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRolePermissionsTables extends Migration
{
    public function up()
    {
        // Tabel roles (jika belum ada)
        if (!$this->db->tableExists('roles')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'nama_role' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'unique' => true,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('roles');
        }
        
        // Tabel permissions (jika belum ada)
        if (!$this->db->tableExists('permissions')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'kode' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'unique' => true,
                ],
                'modul' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'aksi' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('permissions');
        }
        
        // Tabel user_roles (jika belum ada)
        if (!$this->db->tableExists('user_roles')) {
            $this->forge->addField([
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'role_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
            $this->forge->addKey(['user_id', 'role_id'], true);
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('user_roles');
        }
        
        // Tabel role_permissions (jika belum ada)
        if (!$this->db->tableExists('role_permissions')) {
            $this->forge->addField([
                'role_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'permission_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
            $this->forge->addKey(['role_id', 'permission_id'], true);
            $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('role_permissions');
        }
    }

    public function down()
    {
        $this->forge->dropTable('role_permissions', true);
        $this->forge->dropTable('user_roles', true);
        $this->forge->dropTable('permissions', true);
        $this->forge->dropTable('roles', true);
    }
}
