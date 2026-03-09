<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Pradeep Yadav',
                'username'  => 'pradeepy',
                'email'     => 'pradeepy@gmail.com',
                'phone'     => 1234567890,
                'password'  => password_hash('12345678', PASSWORD_DEFAULT),
                'status'    => 1,
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
