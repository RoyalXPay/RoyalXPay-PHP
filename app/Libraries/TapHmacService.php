<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;

class TapHmacService
{
    protected $webhookSecret;
    protected $cache;

    public function __construct()
    {
        $this->webhookSecret = env('TAP_WEBHOOK_SECRET');
        $this->cache = Services::cache();
    }

    /**
     * Generate HMAC signature from webhook payload
     * 
     * @param string $payload Raw webhook payload
     * @return string HMAC signature
     */
    public function generateHmac(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->webhookSecret);
    }

    /**
     * Store HMAC signature for later use
     * Stores in cache with transaction ID as key
     * 
     * @param string $transactionId Transaction ID from webhook
     * @param string $hmac HMAC signature to store
     * @param int $ttl Time to live in seconds (default 24 hours)
     * @return bool Success status
     */
    public function storeHmac(string $transactionId, string $hmac, int $ttl = 86400): bool
    {
        $key = "tap_hmac_{$transactionId}";
        return $this->cache->save($key, $hmac, $ttl);
    }

    /**
     * Retrieve stored HMAC signature
     * 
     * @param string $transactionId Transaction ID
     * @return string|null HMAC signature or null if not found
     */
    public function getStoredHmac(string $transactionId): ?string
    {
        $key = "tap_hmac_{$transactionId}";
        return $this->cache->get($key);
    }

    /**
     * Store webhook payload for reference
     * 
     * @param string $transactionId Transaction ID
     * @param string $payload Raw webhook payload
     * @param int $ttl Time to live in seconds (default 24 hours)
     * @return bool Success status
     */
    public function storeWebhookPayload(string $transactionId, string $payload, int $ttl = 86400): bool
    {
        $key = "tap_webhook_payload_{$transactionId}";
        return $this->cache->save($key, $payload, $ttl);
    }

    /**
     * Get stored webhook payload
     * 
     * @param string $transactionId Transaction ID
     * @return string|null Webhook payload or null if not found
     */
    public function getWebhookPayload(string $transactionId): ?string
    {
        $key = "tap_webhook_payload_{$transactionId}";
        return $this->cache->get($key);
    }

    /**
     * Verify if HMAC matches stored value
     * 
     * @param string $transactionId Transaction ID
     * @param string $hmac HMAC to verify
     * @return bool True if matches
     */
    public function verifyStoredHmac(string $transactionId, string $hmac): bool
    {
        $storedHmac = $this->getStoredHmac($transactionId);
        
        if ($storedHmac === null) {
            return false;
        }

        return hash_equals($storedHmac, $hmac);
    }

    /**
     * Clean up stored data for transaction
     * 
     * @param string $transactionId Transaction ID
     * @return void
     */
    public function cleanup(string $transactionId): void
    {
        $this->cache->delete("tap_hmac_{$transactionId}");
        $this->cache->delete("tap_webhook_payload_{$transactionId}");
    }
}
