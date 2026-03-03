<?php
/**
 * TAP Payment Webhook - Simple Integration Example
 * 
 * This script shows how to send deposit notifications to DITSL/RoyalXPay
 * Modify the variables below with your actual transaction data
 */

// ============================================
// CONFIGURATION
// ============================================
$webhookUrl = 'https://yourdomain.com/api/tap/deposit-webhook'; // Production URL
$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';

// ============================================
// TRANSACTION DATA (Replace with actual data)
// ============================================
$transactionData = [
    'transaction_id' => 'TXN_' . time() . '_' . rand(1000, 9999),
    'amount' => 250.00, // Transaction amount
    'currency' => 'BDT',
    'phone_number' => '880171064443', // Customer wallet phone
    'status' => 'CAPTURED', // CAPTURED = success, pending = processing
    'user_id' => '12345' // Optional: your internal user ID
];

// ============================================
// BUILD WEBHOOK PAYLOAD
// ============================================
$eventId = 'evt_' . time() . '_' . uniqid();

$payload = [
    'id' => $eventId,
    'event' => 'succeeded',
    'data' => [
        'id' => $transactionData['transaction_id'],
        'amount' => $transactionData['amount'],
        'currency' => $transactionData['currency'],
        'status' => $transactionData['status'],
        'customer' => [
            'phone' => [
                'number' => $transactionData['phone_number']
            ]
        ],
        'metadata' => [
            'user_id' => $transactionData['user_id']
        ]
    ]
];

// ============================================
// GENERATE HMAC SIGNATURE
// ============================================
$rawPayload = json_encode($payload);
$signature = hash_hmac('sha256', $rawPayload, $webhookSecret);

echo "Sending webhook to DITSL...\n";
echo "Event ID: $eventId\n";
echo "Transaction ID: {$transactionData['transaction_id']}\n";
echo "Amount: {$transactionData['amount']} {$transactionData['currency']}\n";
echo "Phone: {$transactionData['phone_number']}\n";
echo "Signature: $signature\n\n";

// ============================================
// SEND WEBHOOK REQUEST
// ============================================
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $rawPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Tap-Signature: ' . $signature
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// ============================================
// HANDLE RESPONSE
// ============================================
if ($curlError) {
    echo "CURL Error: $curlError\n";
    exit(1);
}

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode == 200) {
    $responseData = json_decode($response, true);
    if ($responseData['statusCode'] == 200) {
        echo "SUCCESS: Deposit processed successfully\n";
        echo "New Balance: {$responseData['data']['current_balance']} {$responseData['data']['currency']}\n";
    } else {
        echo "Warning: {$responseData['message']}\n";
    }
} else {
    echo "ERROR: Failed to process webhook\n";
}

/*
 * INTEGRATION CHECKLIST:
 * 
 * 1. Replace $webhookUrl with your production endpoint
 * 2. Update $transactionData with actual transaction details
 * 3. Ensure phone number is in format: 880171064443
 * 4. Status should be 'CAPTURED' for successful transactions
 * 5. Each event_id must be unique (prevents duplicates)
 * 6. Store the event_id to avoid reprocessing
 * 7. Handle HTTP errors and retry with exponential backoff
 * 8. Log all webhook attempts for debugging
 */
