<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderDocumentsAndAdminStatus extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'secure_token' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true, 'unique' => true, 'after' => 'ref_id'],
            'admin_status' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'documents_pending', 'after' => 'payment_status'],
            'address' => ['type' => 'TEXT', 'null' => true, 'after' => 'buyer_birthdate'],
            'postal_code' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'address'],
            'national_id_document_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'postal_code'],
            'selfie_document_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'national_id_document_path'],
            'documents_uploaded_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'selfie_document_path'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', [
            'secure_token', 'admin_status', 'address', 'postal_code', 'national_id_document_path', 'selfie_document_path', 'documents_uploaded_at',
        ]);
    }
}
