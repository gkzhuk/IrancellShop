<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuyerNamePartsAndOrderStatusSync extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('orders')) {
            return;
        }

        $columns = [];
        if (!$this->db->fieldExists('buyer_first_name', 'orders')) {
            $columns['buyer_first_name'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true];
        }
        if (!$this->db->fieldExists('buyer_last_name', 'orders')) {
            $columns['buyer_last_name'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true];
        }
        if (!$this->db->fieldExists('order_status', 'orders')) {
            $columns['order_status'] = ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true];
        }

        if ($columns) {
            $this->forge->addColumn('orders', $columns);
        }

        $this->db->query("UPDATE orders SET buyer_first_name = TRIM(SUBSTRING_INDEX(buyer_name, ' ', 1)) WHERE (buyer_first_name IS NULL OR buyer_first_name='') AND buyer_name IS NOT NULL AND buyer_name <> ''");
        $this->db->query("UPDATE orders SET buyer_last_name = TRIM(SUBSTRING(buyer_name, LENGTH(SUBSTRING_INDEX(buyer_name, ' ', 1)) + 1)) WHERE (buyer_last_name IS NULL OR buyer_last_name='') AND buyer_name IS NOT NULL AND buyer_name <> ''");
        $this->db->query("UPDATE orders SET order_status = COALESCE(NULLIF(order_status,''), NULLIF(admin_status,''), 'documents_pending')");
    }

    public function down()
    {
    }
}
