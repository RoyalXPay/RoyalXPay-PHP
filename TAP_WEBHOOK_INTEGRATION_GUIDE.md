# TAP Payment Webhook Integration Guide

## Overview

This document provides integration instructions for TAP Payment Gateway to send deposit notifications to the DITSL/RoyalXPay platform.

---

## Webhook Endpoint Details

### **Production Endpoint**
```
POST https://yourdomain.com/api/tap/deposit-webhook
```

### **Staging/Test Endpoint**
```
POST http://localhost:8080/api/tap/deposit-webhook
```

---

## Authentication

The webhook requires **two layers of authentication**:

### 1. API Key Authentication (Required)
- **Header Name**: `x-api-key`
- **Value**: `99E9418B-6CBD-4574-A646-6415C062DEC7`

### 2. HMAC Signature Verification (Required)
- **Algorithm**: HMAC-SHA256
- **Header Name**: `X-Tap-Signature`
- **Secret Key**: `99E9418B-6CBD-4574-A646-6415C062DEC7`

---

## How to Generate HMAC Signature

### **Step 1: Prepare Request Body**
Take the complete JSON request body as a raw string (no formatting changes).

**Example Request Body:**
```json
{
  "id": "evt_DITSL_123456789",
  "event": "charge.succeeded",
  "object": {
    "id": "chg_DITSL_abc123",
    "amount": 100.00,
    "currency": "BDT",
    "status": "CAPTURED",
    "customer": {
      "phone": {
        "number": "880171064443"
      }
    },
    "metadata": {
      "user_id": "12345"
    }
  }
}
```

### **Step 2: Calculate HMAC-SHA256**

Use the webhook secret key to generate the signature.

**Secret Key**: `99E9418B-6CBD-4574-A646-6415C062DEC7`

#### **PHP Example:**
```php
<?php
$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';

// $requestBody is a PHP array
$requestBody = [
    'id' => 'evt_TAP_TEST_002',
    'event' => 'charge.succeeded',
    'object' => [
        'id' => 'chg_TAP_TEST_002',
        'amount' => 100.00,
        'currency' => 'BDT',
        'status' => 'CAPTURED',
        'customer' => [
            'phone' => [
                'number' => '880171064443'
            ]
        ]
    ]
];

// Convert PHP array to JSON string
$rawPayload = json_encode($requestBody);

// Generate HMAC signature
$signature = hash_hmac('sha256', $rawPayload, $webhookSecret);

// Send request using cURL
$ch = curl_init('https://yourdomain.com/api/tap/deposit-webhook');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $rawPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: 99E9418B-6CBD-4574-A646-6415C062DEC7',
    'X-Tap-Signature: ' . $signature
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
```

