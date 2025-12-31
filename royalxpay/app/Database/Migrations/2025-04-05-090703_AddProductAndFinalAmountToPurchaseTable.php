<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductAndFinalAmountToPurchaseTable extends Migration
{
    public function up()
    {
        $fields = [
            'product_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'after' => 'company_id'
            ],
            'final_total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'after' => 'agent_cbm'
            ]
        ];
        $this->forge->addColumn('purchases', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('purchases', ['product_amount', 'final_total_amount']);
    }
}
