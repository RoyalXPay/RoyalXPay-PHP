<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRemittanceSenderInfoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sender_first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'sender_last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'sender_country_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'sender_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'sender_mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'sender_currency_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'sender_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sender_account_or_card' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'sender_dob' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sender_birth_country' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'sender_id_type' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'sender_id_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'sender_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        
        // Add foreign key constraint
        $this->forge->addForeignKey('transaction_id', 'remittance_transactions', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('remittance_sender_info', true);
    }

    public function down()
    {
        $this->forge->dropTable('remittance_sender_info', true);
    }
}
