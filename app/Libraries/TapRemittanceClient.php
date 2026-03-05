<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

/**
 * TAP Remittance API Client
 * 
 * This client handles all communication with TAP's external Remittance API.
 * It manages authentication, makes HTTP requests, and returns responses.
 */
class TapRemittanceClient
{
    /**
     * @var string TAP API base URL
     */
    protected $baseUrl;

    /**
     * @var string TAP API key
     */
    protected $apiKey;

    /**
     * @var string Bearer token for authenticated requests
     */
    protected $bearerToken;

    /**
     * @var CURLRequest HTTP client
     */
    protected $client;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Base URL should include full path: http://stg-gw.tadlbd.com:885/Remittance/api/v1
        $endpoint = env('TAB_REMITTENCE_URL');
        $this->baseUrl = (env('TAP_REMITTANCE_BASE_URL') . $endpoint);
        $this->apiKey = env('TAP_REMITTANCE_API_KEY');
        $this->client = Services::curlrequest();
    }

    /**
     * Get bearer token from TAP
     * 
     * @param string $username TAP username
     * @param string $password TAP password
     * @return array Response array with statusCode, message, and data
     */
    public function getToken(string $username, string $password): array
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/GetToken', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'username' => $username,
                    'password' => $password
                ],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && isset($body['data']['accessToken'])) {
                return [
                    'statusCode' => $body['statusCode'] ?? 200,
                    'message' => $body['message'] ?? 'SUCCESS',
                    'data' => $body['data']
                ];
            }

            return [
                'statusCode' => $body['statusCode'] ?? $statusCode,
                'message' => $body['message'] ?? 'Failed to get token',
                'data' => null
            ];

        } catch (\Exception $e) {
            log_message('error', 'TAP GetToken failed: ' . $e->getMessage());
            return [
                'statusCode' => 500,
                'message' => 'Token request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Set bearer token for authenticated requests
     * 
     * @param string $token Bearer token
     * @return self
     */
    public function setBearerToken(string $token): self
    {
        $this->bearerToken = $token;
        return $this;
    }

    /**
     * Validate user/wallet with TAP
     * 
     * @param string $walletNumber Wallet number to validate
     * @return array Response array
     */
    public function validateUser(string $walletNumber): array
    {
        return $this->makeRequest('/ValidateUser', [
            'walletNumber' => $walletNumber
        ]);
    }

    /**
     * Push remittance transaction to TAP
     * 
     * @param array $data Transaction data
     * @return array Response array
     */
    public function pushRequestTxn(array $data): array
    {
        return $this->makeRequest('/push-request-txn', $data);
    }

    /**
     * Query transaction status from TAP
     * 
     * @param string $txnRefId Transaction reference ID
     * @return array Response array
     */
    public function txnEnquiry(string $txnRefId): array
    {
        return $this->makeRequest('/TxnEnquiry', [
            'TxnRefId' => $txnRefId
        ]);
    }

    /**
     * Get balance enquiry from TAP
     * 
     * @param string $accountNo Account number
     * @return array Response array
     */
    public function balanceEnquiry(string $accountNo): array
    {
        return $this->makeRequest('/BalanceEnquiry', [
            'accountNo' => $accountNo
        ]);
    }

    /**
     * Get account statement from TAP
     * 
     * @param array $params Parameters (accountNo, fromDate, toDate)
     * @return array Response array
     */
    public function getAccountStatement(array $params): array
    {
        return $this->makeRequest('/GetAccountStatement', $params);
    }

    /**
     * Make authenticated request to TAP API
     * 
     * @param string $endpoint API endpoint (e.g., '/ValidateUser')
     * @param array $data Request payload
     * @return array Response array with statusCode, message, and data
     */
    protected function makeRequest(string $endpoint, array $data): array
    {
        try {
            if (!$this->bearerToken) {
                return [
                    'statusCode' => 401,
                    'message' => 'Bearer token not set. Call setBearerToken() first.',
                    'data' => null
                ];
            }

            $response = $this->client->request('POST', $this->baseUrl . $endpoint, [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Authorization' => 'Bearer ' . $this->bearerToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data,
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            // Log TAP response
            log_message('info', "TAP {$endpoint} Response: " . json_encode($body));

            return [
                'statusCode' => $body['statusCode'] ?? $statusCode,
                'message' => $body['message'] ?? 'Request completed',
                'data' => $body['data'] ?? null
            ];

        } catch (\Exception $e) {
            log_message('error', "TAP {$endpoint} failed: " . $e->getMessage());
            return [
                'statusCode' => 500,
                'message' => 'API request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
