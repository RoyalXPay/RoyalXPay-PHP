<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeyToRemittanceTransactions extends Migration
{
    public function up()
    {
        // Add foreign key constraint from remittance_transactions.account to remittance_wallets.wallet_number
        // Note: wallet_number is VARCHAR and UNIQUE, so we can reference it
        $this->db->query('
            ALTER TABLE remittance_transactions 
            ADD CONSTRAINT fk_remittance_transactions_wallet 
            FOREIGN KEY (account) 
            REFERENCES remittance_wallets(wallet_number) 
            ON DELETE RESTRICT 
            ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        // Drop the foreign key constraint
        $this->db->query('
            ALTER TABLE remittance_transactions 
            DROP FOREIGN KEY fk_remittance_transactions_wallet
        ');
    }
}
