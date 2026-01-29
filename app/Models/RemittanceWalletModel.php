<?php

namespace App\Models;

use CodeIgniter\Model;

class RemittanceWalletModel extends Model
{
    protected $table = 'remittance_wallets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['wallet_number', 'name', 'status', 'balance', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function validateWallet($walletNumber)
    {
        return $this->where('wallet_number', $walletNumber)->first();
    }

    public function updateBalance($walletNumber, $amount)
    {
        $wallet = $this->where('wallet_number', $walletNumber)->first();
        
        if ($wallet) {
            $newBalance = $wallet['balance'] + $amount;
            $this->update($wallet['id'], ['balance' => $newBalance]);
            return $newBalance;
        }
        
        return false;
    }
    
    public function getBalance($walletNumber)
    {
        $wallet = $this->where('wallet_number', $walletNumber)->first();
        return $wallet ? $wallet['balance'] : 0;
    }
}
