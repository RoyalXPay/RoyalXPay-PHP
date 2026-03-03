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

The webhook requires **HMAC Signature Verification** for security:

### HMAC Signature Verification (Required)
- **Algorithm**: HMAC-SHA256
- **Header Name**: `X-Tap-Signature`
- **Secret Key**: `99E9418B-6CBD-4574-A646-6415C062DEC7`

**Note**: No API key is required. The webhook authenticates using only the HMAC signature.

---

## Request Body Structure

The webhook expects the following JSON structure:

- **Required fields at root level:**
  - `id` - Event ID (e.g., `evt_DITSL_123456789`)
  - `event` - Event type (e.g., `charge.succeeded`)
  - `data` - Object containing transaction details

- **Required fields inside `data`:**
  - `id` - Transaction/Charge ID
  - `amount` - Transaction amount (decimal)
  - `currency` - Currency code (e.g., `BDT`)
  - `status` - Transaction status (`CAPTURED` for completed, `pending` for processing)
  - `customer.phone.number` - Customer phone number (wallet identifier)

- **Optional fields:**
  - `metadata.user_id` - User identifier

**Note:** The webhook only accepts the `data` field structure (not `object` or `charge`).

---

## How to Generate HMAC Signature

### **Implementation Flow:**

```
1. Transaction Occurs
   ↓
2. Build JSON payload with transaction data
   {
     "id": "evt_unique_id",
     "event": "succeeded",
     "data": { ... transaction details ... }
   }
   ↓
3. Convert to JSON string
   rawPayload = json_encode($data)
   ↓
4. Calculate HMAC-SHA256
   signature = HMAC_SHA256(rawPayload, webhookSecret)
   ↓
5. Send POST request
   Headers: X-Tap-Signature: {signature}
   Body: {rawPayload}
   ↓
6. Receive response from DITSL
   {
     "statusCode": 200,
     "message": "Deposit processed successfully",
     "data": { ... }
   }
```

---

### **Code Examples:**

### **Step 1: Prepare Request Body**
Take the complete JSON request body as a raw string (no formatting changes).

**Example Request Body:**
```json
{
  "id": "evt_DITSL_123456789",
  "event": "succeeded",
  "data": {
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

#### **PHP Example (Dynamic for any payload):**
```php
<?php
$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';

// Build your request payload dynamically
$eventId = 'evt_' . uniqid(); // Generate unique event ID
$transactionId = 'chg_' . uniqid(); // Generate unique transaction ID
$amount = 150.00; // Dynamic amount
$phoneNumber = '880171064443'; // Customer phone number

$requestBody = [
    'id' => $eventId,
    'event' => 'succeeded',
    'data' => [
        'id' => $transactionId,
        'amount' => $amount,
        'currency' => 'BDT',
        'status' => 'CAPTURED',
        'customer' => [
            'phone' => [
                'number' => $phoneNumber
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
    'X-Tap-Signature: ' . $signature
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "Success: " . $response;
} else {
    echo "Error: HTTP " . $httpCode . " - " . $response;
}
?>
```


## Quick Testing Guide

### **Using Postman:**

1. **Method:** POST
2. **URL:** `http://localhost:8080/api/tap/deposit-webhook`
3. **Headers:**
   ```
   Content-Type: application/json
   X-Tap-Signature: f2c3abde702bfafe8d6efb09365444a3864f4bf0b2518c67476ea3f09675815c
   ```
4. **Body (raw JSON):**
   ```json
   {
     "id": "evt_TAP_TEST_002",
     "event": "succeeded",
     "data": {
       "id": "chg_TAP_TEST_002",
       "amount": 100.00,
       "currency": "BDT",
       "status": "CAPTURED",
       "customer": {
         "phone": {
           "number": "880171064443"
         }
       }
     }
   }
   ```

### **Expected Response (Success):**
```json
{
  "statusCode": 200,
  "message": "Deposit processed successfully",
  "data": {
    "event_id": "DITSL_1772554736_69a709f0e1411",
    "event_type": "wallet.deposit.completed",
    "timestamp": "2026-03-03T20:18:56+04:00",
    "transaction_id": "chg_TAP_TEST_002",
    "external_reference": "evt_TAP_TEST_002",
    "user_id": "1",
    "amount": 100,
    "currency": "BDT",
    "status": "completed",
    "processed_by": "DITSL",
    "current_balance": "1150.00"
  }
}
```

---

## Quick Start for TAP Developers

We've created a ready-to-use example file that you can copy and modify:

**File:** `tap_webhook_example.php`

This example shows:
- How to build the payload dynamically with your transaction data
- Automatic HMAC signature generation
- Complete error handling
- Response processing

**To use:**
1. Open `tap_webhook_example.php`
2. Update the `$transactionData` array with your actual transaction details
3. Run the script: `php tap_webhook_example.php`
4. Check the response and update your integration accordingly

---

## Important Notes

1. **HMAC Signature is Critical**: The signature MUST match the exact request body. Even a single space difference will cause authentication failure.

2. **Use `data` field**: The webhook only accepts the `data` field for transaction details (not `object` or `charge`).

3. **Idempotency**: Each event `id` can only be processed once. Duplicate events will return a `200` response with status `duplicate`.

4. **Phone Number Format**: The wallet is identified by `customer.phone.number` in the format `880171064443`.

5. **Status Values**: 
   - `CAPTURED` or `completed` = Deposit will be credited to wallet
   - `pending` = Deposit recorded but not yet credited
   - Any other status = Deposit recorded but not credited

---

## Support

For integration support, contact the DITSL development team.
