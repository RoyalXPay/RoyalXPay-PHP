<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'pincode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'order_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'shipping_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'cancelled'],
                'default' => 'pending',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('order_id');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('orders', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
