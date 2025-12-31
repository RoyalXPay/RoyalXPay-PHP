<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'module_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'module_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'module_description' => [
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
        $this->forge->addKey('module_id', true);

        // Create table
        $this->forge->createTable('modules', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('modules');
    }
}
