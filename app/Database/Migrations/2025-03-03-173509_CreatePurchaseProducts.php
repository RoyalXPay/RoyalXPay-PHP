<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseProducts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'purchase_product_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'purchase_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'product_master_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'price_per_unit' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('purchase_product_id');
        $this->forge->addForeignKey('purchase_id', 'purchases', 'purchase_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_master_id', 'product_master', 'product_master_id', 'CASCADE', 'CASCADE');

        // Make the serial_number field unique
        $this->forge->addUniqueKey('serial_number');

        $this->forge->createTable('purchase_products', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_products');
    }
}
