<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RepairOrderDocumentSchema extends Migration
{
    public function up()
    {
        $db = $this->db;

        if (!$db->tableExists('orders')) {
            return;
        }

        $columns = [];

        if (!$db->fieldExists('secure_token', 'orders')) {
            $columns['secure_token'] = [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ];
        }

        if (!$db->fieldExists('admin_status', 'orders')) {
            $columns['admin_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'default'    => 'documents_pending',
            ];
        }

        if (!$db->fieldExists('order_status', 'orders')) {
            $columns['order_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
            ];
        }

        if (!$db->fieldExists('address', 'orders')) {
            $columns['address'] = ['type' => 'TEXT', 'null' => true];
        }

        if (!$db->fieldExists('postal_code', 'orders')) {
            $columns['postal_code'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }

        if (!$db->fieldExists('national_id_document_path', 'orders')) {
            $columns['national_id_document_path'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
        }

        if (!$db->fieldExists('selfie_document_path', 'orders')) {
            $columns['selfie_document_path'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
        }

        if (!$db->fieldExists('national_card_image', 'orders')) {
            $columns['national_card_image'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
        }

        if (!$db->fieldExists('selfie_image', 'orders')) {
            $columns['selfie_image'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
        }

        if (!$db->fieldExists('documents_uploaded_at', 'orders')) {
            $columns['documents_uploaded_at'] = ['type' => 'DATETIME', 'null' => true];
        }

        if ($columns !== []) {
            $this->forge->addColumn('orders', $columns);
        }

        // Ensure secure_token is indexed and unique.
        try {
            $this->forge->addKey('secure_token', false, true);
            $this->forge->processIndexes('orders');
        } catch (\Throwable $e) {
            // Ignore if index already exists on target DB engine.
        }
    }

    public function down()
    {
        // Intentionally non-destructive: schema repair migration should not drop live columns.
    }
}
