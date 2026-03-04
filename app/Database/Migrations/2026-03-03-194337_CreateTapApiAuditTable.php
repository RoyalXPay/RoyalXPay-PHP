<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTapApiAuditTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'API endpoint called (transaction-enquiry or reconciliation)',
            ],
            'request_method' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'POST',
            ],
            'request_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'comment' => 'IP address of requester',
            ],
            'request_payload' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON request payload',
            ],
            'transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Transaction ID queried (if applicable)',
            ],
            'external_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'External reference queried (if applicable)',
            ],
            'from_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Reconciliation start date (if applicable)',
            ],
            'to_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Reconciliation end date (if applicable)',
            ],
            'status_filter' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'Status filter used (completed/failed/pending)',
            ],
            'response_status' => [
                'type' => 'INT',
                'constraint' => 3,
                'comment' => 'HTTP response status code',
            ],
            'response_message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Response message',
            ],
            'records_returned' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Number of records returned (for reconciliation)',
            ],
            'total_records' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Total records found (for reconciliation)',
            ],
            'execution_time' => [
                'type' => 'DECIMAL',
                'constraint' => '10,4',
                'null' => true,
                'comment' => 'Query execution time in seconds',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Error details if query failed',
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'User agent string',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('endpoint');
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('external_reference');
        $this->forge->addKey('response_status');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['from_date', 'to_date']);

        $this->forge->createTable('tap_api_audit', true);
    }

    public function down()
    {
        $this->forge->dropTable('tap_api_audit', true);
    }
}
