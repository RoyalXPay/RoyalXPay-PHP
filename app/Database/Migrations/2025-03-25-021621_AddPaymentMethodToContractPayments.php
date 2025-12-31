<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToContractPayments extends Migration
{
    public function up()
    {
        // Adding new columns
        $this->forge->addColumn('contract_payments', [
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['Cash', 'Cheque', 'Online', 'Others'],
                'null'       => false,
                'default'    => 'Cash',
                'after'      => 'payment_status'
            ],
            'cheque_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'payment_method'
            ],
            'cheque_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'cheque_date'
            ],
            'bank_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'cheque_number'
            ],
            'transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'bank_name'
            ],
        ]);
    }

    public function down()
    {
        // Dropping the newly added columns if rolling back
        $this->forge->dropColumn('contract_payments', [
            'payment_method',
            'bank_name',
            'cheque_date',
            'cheque_number',
            'transaction_id'
        ]);
    }
}
