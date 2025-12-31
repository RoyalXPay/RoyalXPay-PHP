<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmodulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'submodule_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'module_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'submodule_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'submodule_description' => [
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
        $this->forge->addKey('submodule_id', true);
        $this->forge->addForeignKey('module_id', 'modules', 'module_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('submodules', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('submodules');
    }
}
