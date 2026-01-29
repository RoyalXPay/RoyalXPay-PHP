<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RemittanceSeeder extends Seeder
{
    public function run()
    {
        $userModel = new \App\Models\UsersModel();
        
        $existingUser = $userModel->where('username', 'TAP')->first();
        
        if (!$existingUser) {
            $userModel->insert([
                'username' => 'TAP',
                'name' => 'TAP API User',
                'email' => 'tap@trustpay.com',
                'phone' => '01700000000',
                'password' => password_hash('123456@', PASSWORD_DEFAULT),
                'user_type' => 'admin',
                'status' => 'active'
            ]);
            
            echo "✓ Created API user: TAP (password: 123456@)\n";
        } else {
            echo "✓ API user TAP already exists\n";
        }

        $walletModel = new \App\Models\RemittanceWalletModel();
        
        $sampleWallets = [
            [
                'wallet_number' => '880171064443',
                'name' => 'Md. Amin',
                'status' => 'Active',
                'balance' => 0.00
            ],
            [
                'wallet_number' => '880181234567',
                'name' => 'John Doe',
                'status' => 'Active',
                'balance' => 0.00
            ],
            [
                'wallet_number' => '880191234567',
                'name' => 'Jane Smith',
                'status' => 'Active',
                'balance' => 0.00
            ]
        ];

        foreach ($sampleWallets as $wallet) {
            $existing = $walletModel->where('wallet_number', $wallet['wallet_number'])->first();
            if (!$existing) {
                $walletModel->insert($wallet);
                echo "✓ Created wallet: {$wallet['wallet_number']} - {$wallet['name']}\n";
            }
        }

        echo "\n✓ Remittance seeder completed!\n";
        echo "✓ You can now test the API with username: TAP, password: 123456@\n";
    }
}
