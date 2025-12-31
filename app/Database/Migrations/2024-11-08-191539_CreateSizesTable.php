<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSizesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'size_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'size_in_inches' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
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
        $this->forge->addKey('size_id', true);

        // Create the table with utf8mb4 charset
        $this->forge->createTable('sizes', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        // Drop the table if it exists
        $this->forge->dropTable('sizes');
    }
}
