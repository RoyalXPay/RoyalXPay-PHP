<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWalletNumberToAccessTokens extends Migration
{
    public function up()
    {
        $fields = [
            'wallet_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'username',
            ],
        ];
        
        $this->forge->addColumn('access_tokens', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('access_tokens', 'wallet_number');
    }
}
