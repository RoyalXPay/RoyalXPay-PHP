<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTransactionIdInRemittanceSenderInfo extends Migration
{
    public function up()
    {
        // Drop the old foreign key first
        $this->forge->dropForeignKey('remittance_sender_info', 'remittance_sender_info_transaction_id_foreign');
        
        // Rename the column
        $this->forge->modifyColumn('remittance_sender_info', [
            'transaction_id' => [
                'name' => 'remittance_transaction_id',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        
        // Add the foreign key back with new column name
        $this->forge->addForeignKey('remittance_transaction_id', 'remittance_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->db->query('ALTER TABLE remittance_sender_info ADD CONSTRAINT remittance_sender_info_remittance_transaction_id_foreign FOREIGN KEY (remittance_transaction_id) REFERENCES remittance_transactions(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop the new foreign key
        $this->forge->dropForeignKey('remittance_sender_info', 'remittance_sender_info_remittance_transaction_id_foreign');
        
        // Rename back to transaction_id
        $this->forge->modifyColumn('remittance_sender_info', [
            'remittance_transaction_id' => [
                'name' => 'transaction_id',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        
        // Add the old foreign key back
        $this->db->query('ALTER TABLE remittance_sender_info ADD CONSTRAINT remittance_sender_info_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES remittance_transactions(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
