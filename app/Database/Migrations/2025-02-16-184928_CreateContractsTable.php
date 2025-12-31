<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContractsTable extends Migration
{
    public function up()
    {
        // Create contracts table
        $this->forge->addField([
            'contract_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'contract_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'contract_type' => [
                'type'       => 'ENUM',
                'constraint' => ['rent', 'sale'],
                'null'       => false,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'vat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'delivery_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'final_payable_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'contract_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'citylight_logo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'citylight_address' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'contact_details' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'citylight_email' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'citylight_website' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'bank_details' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'delivery_time' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'stamp' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'signature' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'return_policies' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'terms_conditions' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'completed', 'canceled'],
                'default' => 'active',
            ],
            'notes' => [
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

        // Add primary key
        $this->forge->addPrimaryKey('contract_id');

        // Create the table with utf8mb4 charset and collation
        $this->forge->createTable('contracts', true, [
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        // Drop the contracts table
        $this->forge->dropTable('contracts');
    }
}
