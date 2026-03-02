<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTapDepositsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'comment' => 'Unique DITSL event identifier for idempotency'
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'comment' => 'wallet.deposit.completed / wallet.deposit.failed / wallet.deposit.pending'
            ],
            'transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'comment' => 'Original transaction reference'
            ],
            'external_reference' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'comment' => 'TAP reference ID (TAP event_id)'
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'comment' => 'Unique user identifier'
            ],
            'wallet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Internal wallet ID'
            ],
            'wallet_number' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'comment' => 'Wallet phone number'
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'comment' => 'Deposit amount'
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => 'GBP',
                'null' => false,
                'comment' => 'Currency code (e.g., GBP, USD)'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['completed', 'failed', 'pending'],
                'default' => 'pending',
                'null' => false,
                'comment' => 'Deposit processing status'
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Error message if processing failed'
            ],
            'processed_by' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'DITSL',
                'null' => false,
                'comment' => 'Must indicate DITSL'
            ],
            'raw_payload' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'comment' => 'Raw JSON payload from TAP for audit'
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'ISO 8601 formatted timestamp when processed'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'comment' => 'When deposit event was received'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('event_id', 'unique_event_id');
        $this->forge->addUniqueKey('external_reference', 'unique_external_ref');
        $this->forge->addKey('wallet_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        
        $this->forge->createTable('tap_deposits', true);
    }

    public function down()
    {
        $this->forge->dropTable('tap_deposits', true);
    }
}
