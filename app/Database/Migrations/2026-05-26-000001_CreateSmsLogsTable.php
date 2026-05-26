<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmsLogsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('sms_logs')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'mobile' => ['type' => 'VARCHAR', 'constraint' => 20],
            'message' => ['type' => 'TEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'api_response' => ['type' => 'TEXT', 'null' => true],
            'related_order_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'event_key' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'sent_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('related_order_id');
        $this->forge->createTable('sms_logs');
    }

    public function down()
    {
        $this->forge->dropTable('sms_logs', true);
    }
}
