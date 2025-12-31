<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentMasterIdMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_master', [
            'parent_master_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'purchase_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('product_master', 'parent_master_id');
    }
}
