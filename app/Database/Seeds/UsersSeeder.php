<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'firstname' => 'Pradeep',
                'lastname' => 'Yadav',
                'email' => 'pradeepy@gmail.com',
                'phone' => 1234567890,
                'password' => password_hash('12345678', PASSWORD_DEFAULT),
                'image' => 'pradeep.jpg',
                'status' => 1,
                'token' => 'pradeepydv',
                'groups' => 'admin',
                'rights' => '1,2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Add more user data as needed
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
