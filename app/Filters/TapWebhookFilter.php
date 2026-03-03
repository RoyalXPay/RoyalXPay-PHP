<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TapWebhookFilter implements FilterInterface
{
    /**
     * Verify TAP webhook HMAC signature
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the webhook secret from environment
        $webhookSecret = env('TAP_WEBHOOK_SECRET');
        
        if (empty($webhookSecret)) {
            log_message('warning', 'TAP_WEBHOOK_SECRET not configured');
            return service('response')->setJSON([
                'statusCode' => 500,
                'message' => 'Webhook configuration error',
                'data' => null
            ])->setStatusCode(500);
        }

        // Get the signature from headers
        $signature = $request->getHeaderLine('X-Tap-Signature');
        if (empty($signature)) {
            $signature = $request->getHeaderLine('X-Webhook-Signature');
        }

        if (empty($signature)) {
            log_message('error', 'No signature header found in TAP webhook request');
            return service('response')->setJSON([
                'statusCode' => 401,
                'message' => 'Missing webhook signature',
                'data' => null
            ])->setStatusCode(401);
        }

        // Get raw payload
        $rawPayload = $request->getBody();

        // Calculate expected signature
        $expectedSignature = hash_hmac('sha256', $rawPayload, $webhookSecret);

        // Compare signatures
        if (!hash_equals($expectedSignature, $signature)) {
            log_message('error', 'Invalid TAP webhook signature');
            return service('response')->setJSON([
                'statusCode' => 401,
                'message' => 'Invalid webhook signature',
                'data' => null
            ])->setStatusCode(401);
        }

        // Signature is valid, allow request to proceed
        log_message('info', 'TAP webhook signature verified successfully');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
