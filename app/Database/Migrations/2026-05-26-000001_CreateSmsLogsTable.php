<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmsLogsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('sms_logs')) {
            if (!$this->db->fieldExists('pattern_code', 'sms_logs')) {
                $this->forge->addColumn('sms_logs', ['pattern_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]]);
            }
            if (!$this->db->fieldExists('request_payload', 'sms_logs')) {
                $this->forge->addColumn('sms_logs', ['request_payload' => ['type' => 'TEXT', 'null' => true]]);
            }
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'related_order_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'mobile' => ['type' => 'VARCHAR', 'constraint' => 20],
            'pattern_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'request_payload' => ['type' => 'TEXT', 'null' => true],
            'api_response' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20],
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
