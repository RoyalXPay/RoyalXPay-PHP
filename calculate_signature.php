<?php

$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';

echo "========================================\n";
echo "HMAC Signature Calculator for TAP Webhook\n";
echo "========================================\n\n";

// Formatted JSON (with 4 spaces indentation)
$formattedJson = '{
    "id": "evt_TAP_TEST_001",
    "event": "succeeded",
    "data": {
        "id": "chg_TAP_TEST_001",
        "amount": 100,
        "currency": "BDT",
        "status": "CAPTURED",
        "customer": {
            "phone": {
                "number": "880171064443"
            }
        }
    }
}';

$signature = hash_hmac('sha256', $formattedJson, $webhookSecret);

echo "Formatted JSON (4-space indentation)\n";
echo "-------------------------------------------\n";
echo "JSON Body:\n";
echo $formattedJson . "\n\n";
echo "Generated HMAC Signature:\n";
echo $signature . "\n\n";
echo "Postman Header:\n";
echo "X-Tap-Signature: " . $signature . "\n";
echo "-------------------------------------------\n\n";

echo "IMPORTANT:\n";
echo "Copy the EXACT JSON above (with 4-space indentation) to Postman body.\n";
echo "Use the signature shown above in the X-Tap-Signature header.\n";
echo "========================================\n";

