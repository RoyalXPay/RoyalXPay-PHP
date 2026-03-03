<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSenderColumnsFromRemittanceTransactions extends Migration
{
    public function up()
    {
        // Drop sender info columns (we moved them to separate table)
        $fields = [
            'sender_first_name',
            'sender_last_name',
            'sender_country_code',
            'sender_email',
            'sender_mobile',
            'sender_currency_code',
            'sender_address',
            'sender_account_or_card',
            'sender_dob',
            'sender_birth_country',
            'sender_id_type',
            'sender_id_number',
            'sender_amount',
        ];

        $this->forge->dropColumn('remittance_transactions', $fields);
    }

    public function down()
    {
        // Restore columns if needed to rollback
        $fields = [
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
        ];

        $this->forge->addColumn('remittance_transactions', $fields);
    }
}
