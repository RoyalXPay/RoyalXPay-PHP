<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddModuleAccessToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'module_access' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'permissions',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // Rollback migration
        $this->forge->dropColumn('users', 'module_access');
    }
}
