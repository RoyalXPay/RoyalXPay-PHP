<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGlassTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'glass_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
            ],
            'description' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('glass_id', true);

        // Create the table with utf8mb4 charset
        $this->forge->createTable('glass_types', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        // Drop the table if it exists
        $this->forge->dropTable('glass_types');
    }
}
