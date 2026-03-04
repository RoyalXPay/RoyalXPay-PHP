<?php

$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';

echo "========================================\n";
echo "HMAC Signature Calculator\n";
echo "Endpoint: /api/v1/tap/reconciliation\n";
echo "========================================\n\n";

// Request payload - COMPACT JSON
$requestPayload = '{"from_date":"2026-03-01","to_date":"2026-03-04","status":"completed","limit":100}';

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