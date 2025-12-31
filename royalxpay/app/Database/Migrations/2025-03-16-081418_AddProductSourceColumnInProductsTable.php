<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductSourceColumnInProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'product_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'after'      => 'product_type',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'product_source');
    }
}
