<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRemittanceTransactionsTable extends Migration
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
            'org_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'process_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'biller_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'reference_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'account' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'principal_ref_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'trx_ref_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'PENDING',
            ],
            'transaction_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('account');
        $this->forge->createTable('remittance_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('remittance_transactions', true);
    }
}
