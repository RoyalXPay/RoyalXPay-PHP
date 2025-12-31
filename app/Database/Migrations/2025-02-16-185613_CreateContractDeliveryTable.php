<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContractDeliveryTable extends Migration
{
    public function up()
    {
        // Define the contract_delivery table
        $this->forge->addField([
            'delivery_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'contract_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'delivery_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'delivery_agent' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'signature' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'delivery_status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Partially Delivered', 'Delivered', 'Rejected'],
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        // Adding Primary Key
        $this->forge->addPrimaryKey('delivery_id');
        // Foreign Key to link contract_id with contracts table
        $this->forge->addForeignKey('contract_id', 'contracts', 'contract_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contract_delivery', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        // Drop the contract_delivery table if rolling back
        $this->forge->dropTable('contract_delivery');
    }
}
