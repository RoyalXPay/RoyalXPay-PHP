<?php

namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table = 'addresses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['user_id', 'address', 'city', 'state', 'postal_code', 'country', 'address_type', 'created_at', 'updated_at'];

    // Fetch all addresses for a user
    public function getUserAddresses($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    // Fetch billing address for a user
    public function getBillingAddress($userId)
    {
        return $this->where(['user_id' => $userId, 'address_type' => 'billing'])->first();
    }

    // Fetch shipping address for a user
    public function getShippingAddress($userId)
    {
        return $this->where(['user_id' => $userId, 'address_type' => 'shipping'])->first();
    }
}
