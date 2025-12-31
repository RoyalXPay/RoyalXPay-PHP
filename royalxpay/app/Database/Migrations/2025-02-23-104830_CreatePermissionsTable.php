<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
                'unsigned'       => true
            ],
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'module_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'submodule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('role_id', 'roles', 'role_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('module_id', 'modules', 'module_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('submodule_id', 'submodules', 'submodule_id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('permissions');
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}
