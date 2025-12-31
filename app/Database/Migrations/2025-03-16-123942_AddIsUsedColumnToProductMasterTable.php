<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsUsedColumnToProductMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_master', [
            'is_used' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'additional_information',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('product_master', 'is_used');
    }
}
