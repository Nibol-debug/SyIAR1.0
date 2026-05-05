<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'login_status' => ['type' => 'ENUM', 'constraint' => ['success', 'failed'], 'default' => 'success'],
            'failure_reason' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'login_time' => ['type' => 'DATETIME'],
            'logout_time' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['user_id', 'login_time']);
        $this->forge->createTable('login_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('login_logs', true);
    }
}
