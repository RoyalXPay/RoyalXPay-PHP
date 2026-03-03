# TAP Webhook Integration - Quick Summary

## For TAP Development Team

This folder contains everything you need to integrate with DITSL/RoyalXPay webhook.

---

## 📁 Files Provided

1. **TAP_WEBHOOK_INTEGRATION_GUIDE.md** - Complete integration documentation
2. **tap_webhook_example.php** - Ready-to-use PHP example (copy & modify)
3. **calculate_signature.php** - HMAC signature calculator for testing

---

## 🚀 Quick Start (3 Steps)

### Step 1: Understand the Flow
```
Your System → Build JSON → Calculate HMAC → Send POST → Receive Response
```

### Step 2: Test with Example
```bash
# Edit tap_webhook_example.php with your transaction data
php tap_webhook_example.php
```

### Step 3: Integrate into Your System

**Key Function to Implement:**

```php
function sendDepositWebhook($transactionId, $amount, $phoneNumber, $status) {
    $webhookUrl = 'https://yourdomain.com/api/tap/deposit-webhook';
    $webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';
    
    // Build payload
    $payload = [
        'id' => 'evt_' . time() . '_' . uniqid(),
        'event' => 'succeeded',
        'data' => [
            'id' => $transactionId,
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => $status, // 'CAPTURED' or 'pending'
            'customer' => [
                'phone' => ['number' => $phoneNumber]
            ]
        ]
    ];
    
    // Generate signature
    $rawPayload = json_encode($payload);
    $signature = hash_hmac('sha256', $rawPayload, $webhookSecret);
    
    // Send request
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
    curl_close($ch);
    
    return ['code' => $httpCode, 'response' => json_decode($response, true)];
}

// Usage
$result = sendDepositWebhook('TXN_12345', 100.00, '880171064443', 'CAPTURED');
```

---

## 🔑 Critical Information

### Endpoint
```
POST https://yourdomain.com/api/tap/deposit-webhook
```

### Required Header
```
X-Tap-Signature: {hmac_sha256_signature}
```

### Webhook Secret
```
99E9418B-6CBD-4574-A646-6415C062DEC7
```

### Payload Format
```json
{
    "id": "evt_unique_id",
    "event": "succeeded",
    "data": {
        "id": "transaction_id",
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

---

## ✅ Testing Checklist

- [ ] Calculate HMAC signature correctly for each request
- [ ] Use unique `event_id` for each webhook call
- [ ] Phone number format: 880XXXXXXXXX (no + or spaces)
- [ ] Status: 'CAPTURED' for successful, 'pending' for processing
- [ ] Test with `calculate_signature.php` first
- [ ] Handle HTTP 200 success response
- [ ] Handle HTTP 401 (invalid signature)
- [ ] Handle HTTP 400 (invalid payload)
- [ ] Implement retry logic with exponential backoff
- [ ] Log all webhook attempts

---

## 📊 Expected Responses

### Success (HTTP 200)
```json
{
    "statusCode": 200,
    "message": "Deposit processed successfully",
    "data": {
        "event_id": "DITSL_1772554736_69a709f0e1411",
        "current_balance": "1150.00",
        "status": "completed"
    }
}
```

### Duplicate Event (HTTP 200)
```json
{
    "statusCode": 200,
    "message": "Event already processed",
    "data": {
        "event_id": "evt_TAP_TEST_002",
        "status": "duplicate"
    }
}
```

### Invalid Signature (HTTP 401)
```json
{
    "statusCode": 401,
    "message": "Invalid webhook signature",
    "data": null
}
```

### Wallet Not Found (HTTP 400)
```json
{
    "statusCode": 400,
    "message": "Wallet not found",
    "data": null
}
```

---

## 🛠️ Troubleshooting

### Issue: "Invalid webhook signature"
- **Cause**: HMAC signature doesn't match the request body
- **Solution**: Ensure you calculate HMAC from the exact JSON string being sent
- **Test**: Use `calculate_signature.php` to verify your signature

### Issue: "Wallet not found"
- **Cause**: Phone number doesn't match any wallet in system
- **Solution**: Verify phone number format (880XXXXXXXXX)

### Issue: "Event already processed"
- **Cause**: Duplicate event_id sent
- **Solution**: Generate unique event_id for each webhook call

---

## 📞 Support

For integration support or questions:
- Technical Documentation: `TAP_WEBHOOK_INTEGRATION_GUIDE.md`
- Example Code: `tap_webhook_example.php`
- Test Tool: `calculate_signature.php`

---

## 🎯 Production Deployment

Before going live:

1. **Update Production URL** in your code
2. **Secure the webhook secret** (use environment variables)
3. **Implement error logging** and monitoring
4. **Add retry mechanism** for failed webhooks
5. **Test with real transactions** in staging environment
6. **Monitor first 100 transactions** closely
7. **Set up alerts** for webhook failures

---

**Last Updated:** 3 March 2026  
**Version:** 1.0
