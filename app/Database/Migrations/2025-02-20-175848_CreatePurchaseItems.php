<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'purchase_item_id' => [
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
            'price_per_unit' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'cbm_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
            ],
            'shipment_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('purchase_item_id');
        $this->forge->addForeignKey('purchase_id', 'purchases', 'purchase_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_master_id', 'product_master', 'product_master_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_items', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_items');
    }
}
