<?php

$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';

echo "========================================\n";
echo "HMAC Signature Calculator\n";
echo "Endpoint: /api/v1/tap/transaction-enquiry\n";
echo "========================================\n\n";

// Request payload - COMPACT JSON
$requestPayload = '{"transaction_id":"chg_TAP_TEST_001"}';

// Generate HMAC signature from compact JSON
$signature = hash_hmac('sha256', $requestPayload, $webhookSecret);

echo "\n-------------------------------------------\n";
echo "JSON Payload (COMPACT - for production):\n";
echo "-------------------------------------------\n";
echo $requestPayload . "\n\n";

echo "-------------------------------------------\n";
echo "Generated HMAC Signature:\n";
echo "-------------------------------------------\n";
echo $signature . "\n\n";

