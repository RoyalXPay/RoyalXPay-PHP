<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotationsProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'quotation_product_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'quotation_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'product_master_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'product_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'quantity' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 1,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
        ]);

        // Add Primary Key
        $this->forge->addPrimaryKey('quotation_product_id');

        // Add Foreign Key Constraints
        $this->forge->addForeignKey('quotation_id', 'quotations', 'quotation_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_master_id', 'product_master', 'product_master_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');

        // Create the table with utf8mb4 encoding
        $this->forge->createTable('quotation_products', true, [
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        // Drop the quotation_products table
        $this->forge->dropTable('quotation_products');
    }
}
