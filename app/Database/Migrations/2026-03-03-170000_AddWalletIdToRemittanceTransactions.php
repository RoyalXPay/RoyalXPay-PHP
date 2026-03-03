<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWalletIdToRemittanceTransactions extends Migration
{
    public function up()
    {
        // First, drop the existing foreign key if it exists
        try {
            $this->db->query('
                ALTER TABLE remittance_transactions 
                DROP FOREIGN KEY fk_remittance_transactions_wallet
            ');
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }

        // Add wallet_id column right after id
        $fields = [
            'wallet_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ];
        
        $this->forge->addColumn('remittance_transactions', $fields);

        // Populate wallet_id from existing account data
        $this->db->query('
            UPDATE remittance_transactions rt
            INNER JOIN remittance_wallets rw ON rw.wallet_number = rt.account
            SET rt.wallet_id = rw.id
        ');

        // Now make wallet_id NOT NULL after population
        $this->db->query('
            ALTER TABLE remittance_transactions 
            MODIFY wallet_id INT(11) UNSIGNED NOT NULL
        ');

        // Add foreign key constraint
        $this->db->query('
            ALTER TABLE remittance_transactions 
            ADD CONSTRAINT fk_remittance_transactions_wallet_id 
            FOREIGN KEY (wallet_id) 
            REFERENCES remittance_wallets(id) 
            ON DELETE RESTRICT 
            ON UPDATE CASCADE
        ');

        // Add index on wallet_id for better query performance
        $this->forge->addKey('wallet_id');
        $this->db->query('ALTER TABLE remittance_transactions ADD INDEX idx_wallet_id (wallet_id)');
    }

    public function down()
    {
        // Drop foreign key
        $this->db->query('
            ALTER TABLE remittance_transactions 
            DROP FOREIGN KEY fk_remittance_transactions_wallet_id
        ');

        // Drop index
        $this->db->query('
            ALTER TABLE remittance_transactions 
            DROP INDEX idx_wallet_id
        ');

        // Drop column
        $this->forge->dropColumn('remittance_transactions', 'wallet_id');

        // Re-add the old foreign key on account
        $this->db->query('
            ALTER TABLE remittance_transactions 
            ADD CONSTRAINT fk_remittance_transactions_wallet 
            FOREIGN KEY (account) 
            REFERENCES remittance_wallets(wallet_number) 
            ON DELETE RESTRICT 
            ON UPDATE CASCADE
        ');
    }
}
