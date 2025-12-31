<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContractProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'contract_product_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'contract_id' => [
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

        $this->forge->addPrimaryKey('contract_product_id');
        $this->forge->addForeignKey('contract_id', 'contracts', 'contract_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_master_id', 'product_master', 'product_master_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('contract_products', true, [
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        // Drop the contracts table
        $this->forge->dropTable('contract_products');
    }
}
