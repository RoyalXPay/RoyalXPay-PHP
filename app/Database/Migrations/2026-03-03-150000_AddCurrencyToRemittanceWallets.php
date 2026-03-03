<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCurrencyToRemittanceWallets extends Migration
{
    public function up()
    {
        $fields = [
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'BDT',
                'after'      => 'balance',
            ],
        ];

        $this->forge->addColumn('remittance_wallets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('remittance_wallets', 'currency');
    }
}
