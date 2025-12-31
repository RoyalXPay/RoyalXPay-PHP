<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class GeneralApiController extends ResourceController
{
    use ResponseTrait;

    // Public method to call from frontend or controller
    public function getMerchantWalletBalance()
    {
        $transactionId = $this->generateTransactionId();
        $token = $this->getMbmeTokenDirect();

        if (!$token || !$transactionId) {
            return $this->fail('Token generation or Transaction ID failed.', 500);
        }

        $balance = $this->getMerchantBalanceFromMbme($token, $transactionId);

        return $this->respond([
            'wallet_balance' => $balance,
            'transaction_id' => $transactionId
        ]);
    }

    // Token generation function
    private function getMbmeTokenDirect()
    {
        $client = \Config\Services::curlrequest();

        $postData = http_build_query([
            'grant_type' => 'password',
            'username'   => 'DBAPSP',
            'password'   => 'y08yhFn1LC'
        ]);

        try {
            $response = $client->post('https://qty.mbme.org:8080/v2/mbme/oauth/token', [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body'    => $postData
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['access_token'] ?? $result['accessToken'] ?? null;

        } catch (\Exception $e) {
            log_message('error', 'Token error: ' . $e->getMessage());
            return null;
        }
    }

    // Merchant wallet balance fetch
    private function getMerchantBalanceFromMbme($token, $transactionId)
    {
        $client = \Config\Services::curlrequest();

        $body = json_encode([
            'transactionId' => $transactionId
        ]);

        try {
            $response = $client->post('https://qty.mbme.org:8080/v2/api/mbme/merchantBalance', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json'
                ],
                'body' => $body,
                'http_errors' => false
            ]);

            $result = json_decode($response->getBody(), true);

            if (isset($result['walletBalance']) && is_numeric($result['walletBalance'])) {
                return floatval($result['walletBalance']);
            }

            return 0.00;
        } catch (\Exception $e) {
            log_message('error', 'Merchant balance fetch failed: ' . $e->getMessage());
            return 0.00;
        }
    }

    // Transaction ID generator (UAE time)
    private function generateTransactionId()
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Dubai'));

        $YY = $now->format('y');
        $MM = $now->format('m');
        $DD = $now->format('d');
        $HH = $now->format('H');
        $mm = $now->format('i');
        $SS = $now->format('s');
        $MS = substr((string)microtime(true), -2);
        $randomDigit = rand(0, 9);

        return "{$YY}{$MM}{$DD}{$HH}{$mm}{$SS}{$MS}{$randomDigit}3684";
    }
}
