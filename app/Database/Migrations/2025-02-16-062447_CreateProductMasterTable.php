<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_master_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'product_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => false,
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'discount_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'final_product_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'size_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'color_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'touch_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'glass_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'resolution_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'product_images' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'additional_information' => [
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

        // Primary key
        $this->forge->addPrimaryKey('product_master_id');

        // Add foreign key constraints
        $this->forge->addForeignKey('size_id', 'sizes', 'size_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('color_id', 'colors', 'color_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('touch_id', 'touch_types', 'touch_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('glass_id', 'glass_types', 'glass_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('resolution_id', 'resolutions', 'resolution_id', 'CASCADE', 'SET NULL');

        // Create the table with utf8mb4 charset
        $this->forge->createTable('product_master', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('product_master');
    }
}
