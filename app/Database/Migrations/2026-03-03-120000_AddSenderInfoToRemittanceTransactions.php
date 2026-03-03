<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSenderInfoToRemittanceTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'signature' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'channel',
            ],
            'notification_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'signature',
            ],
            'sender_first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'principal_ref_id',
            ],
            'sender_last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'sender_first_name',
            ],
            'sender_country_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'sender_last_name',
            ],
            'sender_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'sender_country_code',
            ],
            'sender_mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'sender_email',
            ],
            'sender_currency_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'sender_mobile',
            ],
            'sender_address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'sender_currency_code',
            ],
            'sender_account_or_card' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'sender_address',
            ],
            'sender_dob' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'sender_account_or_card',
            ],
            'sender_birth_country' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'sender_dob',
            ],
            'sender_id_type' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'sender_birth_country',
            ],
            'sender_id_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'sender_id_type',
            ],
            'sender_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'sender_id_number',
            ],
        ];

        $this->forge->addColumn('remittance_transactions', $fields);
    }

    public function down()
    {
        $fields = [
            'signature',
            'notification_no',
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
}
