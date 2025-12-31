<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        // Create the products table
        $this->forge->addField([
            'product_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_master_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'product_type' => [
                'type' => 'ENUM',
                'constraint' => ['rent', 'sale'],
                'null' => true,
            ],
            'price_per_day' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'           => true,
            ],
            'product_price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,3',
                'null'           => true,
            ],
            'discount_percentage' => [
                'type'           => 'INT',
                'constraint'     => '3',
                'null'           => true,
            ],
            'final_product_price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,3',
                'null'           => true,
            ],
            'start_date' => [
                'type'      => 'DATE',
                'null'      => true,
            ],
            'end_date' => [
                'type'      => 'DATE',
                'null'      => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'available_quantity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        // Add primary key
        $this->forge->addPrimaryKey('product_id');
        $this->forge->addForeignKey('product_master_id', 'product_master', 'product_master_id', 'CASCADE', 'CASCADE');

        // Create the table
        $this->forge->createTable('products', true, [
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        // Drop the products table
        $this->forge->dropTable('products');
    }
}
