<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanyInfo extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'citylight_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'citylight_address' => [
                'type' => 'TEXT',
            ],
            'contact_details' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'citylight_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'bank_details' => [
                'type' => 'TEXT',
            ],
            'terms_and_conditions' => [
                'type' => 'TEXT',
            ],
            'return_policies' => [
                'type' => 'TEXT',
            ],
            'signature' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'stamp' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('company_info', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
        
    }

    public function down()
    {
        $this->forge->dropTable('company_info');
    }
}
