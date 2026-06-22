<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInventoryImportAndDiscounts extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('simcards')) {
            $fields = $this->db->getFieldNames('simcards');
            $addFields = [];

            if (!in_array('original_price', $fields, true)) {
                $addFields['original_price'] = [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'price',
                ];
            }

            if (!in_array('discount_percent', $fields, true)) {
                $addFields['discount_percent'] = [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => '0.00',
                    'after'      => 'original_price',
                ];
            }

            if (!in_array('final_price', $fields, true)) {
                $addFields['final_price'] = [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'discount_percent',
                ];
            }

            if (!in_array('discount_starts_at', $fields, true)) {
                $addFields['discount_starts_at'] = [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'final_price',
                ];
            }

            if (!in_array('discount_ends_at', $fields, true)) {
                $addFields['discount_ends_at'] = [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'discount_starts_at',
                ];
            }

            if (!in_array('discount_enabled', $fields, true)) {
                $addFields['discount_enabled'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'discount_ends_at',
                ];
            }

            if (!empty($addFields)) {
                $this->forge->addColumn('simcards', $addFields);
            }

            try {
                $this->db->query('CREATE INDEX idx_simcards_number ON simcards (number)');
            } catch (\Throwable $e) {
                log_message('debug', 'simcards number index already exists or could not be created: ' . $e->getMessage());
            }
        }

        if (!$this->db->tableExists('excel_import_jobs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'file_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'original_filename' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'pending',
                ],
                'total_rows' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'processed_rows' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'inserted_rows' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'updated_rows' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'failed_rows' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'current_row' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 2,
                ],
                'chunk_size' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 300,
                ],
                'error_log' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'started_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'completed_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'last_processed_at' => [
                    'type' => 'DATETIME',
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
            $this->forge->addKey('status');
            $this->forge->createTable('excel_import_jobs');
        }

        if (!$this->db->tableExists('inventory_settings')) {
            $this->forge->addField([
                'setting_key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'setting_value' => [
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
            $this->forge->addKey('setting_key', true);
            $this->forge->createTable('inventory_settings');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('excel_import_jobs')) {
            $this->forge->dropTable('excel_import_jobs');
        }

        if ($this->db->tableExists('inventory_settings')) {
            $this->forge->dropTable('inventory_settings');
        }
    }
}
