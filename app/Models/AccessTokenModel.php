<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessTokenModel extends Model
{
    protected $table = 'access_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'username', 'token', 'expires_at', 'created_at'];
    protected $useTimestamps = false;

    public function generateToken($userId, $username)
    {
        $token = bin2hex(random_bytes(32));

        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->where('user_id', $userId)->delete();
        
        $this->insert([
            'user_id' => $userId,
            'username' => $username,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return [
            'token' => $token,
            'expires_at' => $expiresAt
        ];
    }

    public function validateToken($token)
    {
        return $this->where('token', $token)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    public function revokeToken($token)
    {
        return $this->where('token', $token)->delete();
    }

    /**
     * Clean up expired tokens (run as cron job)
     */
    // public function cleanExpiredTokens()
    // {
    //     return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    // }
}
