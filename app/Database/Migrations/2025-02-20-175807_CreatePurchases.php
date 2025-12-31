<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchases extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'purchase_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'company_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'export_wooden_case' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'send_to_warehouse' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'payment_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['bank_transfer', 'online', 'cash', 'other'],
                'null' => true,
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['paid', 'pending', 'partially_paid', 'not_paid'],
                'null' => true,
            ],
            'uploaded_receipt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'purchase_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'stopped', 'cancelled'],
                'null' => true,
            ],
            'was_china_paid' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'null' => true,
            ],
            'agent_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'agent_cbm' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'agent_payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['bank_transfer', 'online', 'cash', 'other'],
                'null' => true,
            ],
            'agent_payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['paid', 'pending', 'partially_paid', 'not_paid'],
                'null' => true,
            ],
            'agent_attachment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'agent_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'agent_status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'pending', 'suspended'],
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('purchase_id');
        $this->forge->createTable('purchases', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('purchases');
    }
}
