<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductTypeAndPurchaseIdToProductMaster extends Migration
{
    public function up()
    {
        // Adding the product_type ENUM column with values 'master' and 'purchase' and purchase_id column using raw SQL
        $this->db->query('
            ALTER TABLE product_master
            ADD COLUMN product_type ENUM("master", "purchase") NOT NULL DEFAULT "master" AFTER product_master_id,
            ADD COLUMN purchase_id INT UNSIGNED NULL AFTER product_type;
        ');
    }

    public function down()
    {
        // Dropping the added columns if we rollback the migration
        $this->db->query('
            ALTER TABLE product_master
            DROP COLUMN product_type,
            DROP COLUMN purchase_id;
        ');
    }
}
