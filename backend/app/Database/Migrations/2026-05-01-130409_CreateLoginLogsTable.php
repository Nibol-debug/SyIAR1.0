<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'user_agent' => [
                'type' => 'TEXT',
            ],
            'login_time' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['success', 'failed'],
                'default' => 'success',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('login_logs');
    }

    public function down()
    {
        $this->forge->dropTable('login_logs');
    }
}