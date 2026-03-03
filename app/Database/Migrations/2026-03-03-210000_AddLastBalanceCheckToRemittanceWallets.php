<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastBalanceCheckToRemittanceWallets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('remittance_wallets', [
            'last_balance_check' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
                'comment' => 'Last time balance was checked from TAP API'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('remittance_wallets', 'last_balance_check');
    }
}
