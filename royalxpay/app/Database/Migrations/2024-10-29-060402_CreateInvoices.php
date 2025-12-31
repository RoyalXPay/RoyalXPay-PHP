<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'invoice_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invoice_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'rent_or_sale' => [
                'type' => 'ENUM',
                'constraint' => ['Rent', 'Sale'],
            ],
            'invoice_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'amount_per_unit' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'paid_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'vat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'total_paid_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'citylight_logo' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'citylight_address' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'contact_details' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'citylight_email' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'citylight_website' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'bank_details' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'terms_and_conditions' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'return_policies' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'signature' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'stamp' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('invoice_id');
        $this->forge->createTable('invoices', true, ['DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('invoices');
    }
}
