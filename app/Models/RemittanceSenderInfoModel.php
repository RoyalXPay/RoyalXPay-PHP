<?php

namespace App\Models;

use CodeIgniter\Model;

class RemittanceSenderInfoModel extends Model
{
    protected $table = 'remittance_sender_info';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'remittance_transaction_id',
        'sender_first_name',
        'sender_last_name',
        'sender_country_code',
        'sender_email',
        'sender_mobile',
        'sender_currency_code',
        'sender_address',
        'sender_account_or_card',
        'sender_dob',
        'sender_birth_country',
        'sender_id_type',
        'sender_id_number',
        'sender_amount',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Create sender info record
     */
    public function createSenderInfo($transactionId, $senderInfo)
    {
        $data = [
            'remittance_transaction_id' => $transactionId,
            'sender_first_name' => $senderInfo['senderFirstName'] ?? null,
            'sender_last_name' => $senderInfo['senderLastName'] ?? null,
            'sender_country_code' => $senderInfo['senderCountryCode'] ?? null,
            'sender_email' => $senderInfo['senderEmail'] ?? null,
            'sender_mobile' => $senderInfo['senderMobile'] ?? null,
            'sender_currency_code' => $senderInfo['senderCurrencyCode'] ?? null,
            'sender_address' => $senderInfo['senderAddress'] ?? null,
            'sender_account_or_card' => $senderInfo['senderAccountOrCard'] ?? null,
            'sender_dob' => $senderInfo['senderDOB'] ?? null,
            'sender_birth_country' => $senderInfo['senderBirthCountry'] ?? null,
            'sender_id_type' => $senderInfo['senderIDType'] ?? null,
            'sender_id_number' => $senderInfo['senderIDNumber'] ?? null,
            'sender_amount' => $senderInfo['senderAmount'] ?? null,
        ];

        return $this->insert($data);
    }

    /**
     * Get sender info by transaction ID
     */
    public function getSenderInfoByTransaction($transactionId)
    {
        return $this->where('remittance_transaction_id', $transactionId)->first();
    }
}
