<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeletedQuotationsTable extends Migration
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
            'quotation_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('deleted_quotations', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('deleted_quotations');
    }
}
