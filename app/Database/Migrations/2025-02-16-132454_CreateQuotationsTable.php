<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotationsTable extends Migration
{
    public function up()
    {
        // Create quotations table
        $this->forge->addField([
            'quotation_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'quotation_code' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
            ],
            'user_id' => [
                'type'          => 'INT',
                'unsigned'      => true,
                'null'          => false,
            ],
            'quotation_type' => [
                'type'           => 'ENUM',
                'constraint'    => ['rent', 'sale'],
                'null'          => false,
            ],
            'start_date' => [
                'type'          => 'DATE',
                'null'          => true,
            ],
            'end_date' => [
                'type'          => 'DATE',
                'null'          => true,
            ],
            'total_price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,3',
            ],
            'vat' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,3',
            ],
            'discount' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'null'           => true,
            ],
            'final_payable_price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,3',
            ],
            'quotation_date' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
            'citylight_logo' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'citylight_address' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'contact_details' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'citylight_email' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'citylight_website' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'bank_details' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'delivery_time' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'stamp' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'signature' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'return_policies' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'terms_conditions' => [
                'type'           => 'TINYINT',
                'default'        => 0,
            ],
            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['active', 'completed', 'canceled'],
                'default'        => 'active',
            ],
            'notes' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        // Add Primary Key
        $this->forge->addPrimaryKey('quotation_id');

        // Create the table with utf8mb4 encoding
        $this->forge->createTable('quotations', true, [
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci'
        ]);
    }

    public function down()
    {
        // Drop the quotations table
        $this->forge->dropTable('quotations');
    }
}
