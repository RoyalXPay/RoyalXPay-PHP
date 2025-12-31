<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContractPaymentsTable extends Migration
{
    public function up()
    {
        // Define the contract_payments table
        $this->forge->addField([
            'payment_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'contract_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'payment_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'final_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'payment_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'balance_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Partially Paid', 'Completed', 'Rejected'],
                'null' => true,
            ],
            'receipt' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->addPrimaryKey('payment_id');
        $this->forge->addForeignKey('contract_id', 'contracts', 'contract_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contract_payments', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        // Drop the contract_payments table if rolling back
        $this->forge->dropTable('contract_payments');
    }
}
