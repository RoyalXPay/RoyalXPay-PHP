<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBanksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bank_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'account_holder' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'account_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'ifsc_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'swift_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'branch_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary key
        $this->forge->addKey('bank_id', true);

        // Create the table with utf8mb4 charset
        $this->forge->createTable('banks', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        // Drop the table if it exists
        $this->forge->dropTable('banks');
    }
}
