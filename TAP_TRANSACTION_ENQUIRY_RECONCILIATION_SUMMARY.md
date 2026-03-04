# TAP Transaction Enquiry & Reconciliation API - Integration Summary

## Overview

This document provides a complete integration summary for TAP's Transaction Enquiry and Reconciliation APIs. These endpoints allow TAP to query transaction status and retrieve bulk transaction data for reconciliation purposes.

**Base URL:** `https://yourdomain.com/api/v1/tap`

**Authentication:** HMAC SHA256 signature verification (same as webhook)

**Response Format:** JSON

---

## 1. Transaction Enquiry API

### Endpoint
```
POST /api/v1/tap/transaction-enquiry
```

### Purpose
Query the status and details of a single transaction by transaction ID or external reference.

### Authentication
- **Header:** `X-Tap-Signature` or `X-Webhook-Signature`
- **Method:** HMAC SHA256
- **Secret:** `99E9418B-6CBD-4574-A646-6415C062DEC7`

### Request Headers
```
Content-Type: application/json
X-Tap-Signature: <HMAC_SHA256_signature>
```

### Request Body
```json
{
    "transaction_id": "chg_TAP_TEST_001",
    "external_reference": "DITSL_TAP_TEST_001"
}
```

**Parameters:**
- `transaction_id` (string, optional): TAP transaction charge ID
- `external_reference` (string, optional): External reference ID

**Note:** At least one identifier (`transaction_id` or `external_reference`) is required.

### Response - Success (200)
```json
{
    "statusCode": 200,
    "message": "Transaction found",
    "data": {
        "transaction": {
            "id": 1,
            "event_id": "DITSL_TAP_TEST_001",
            "event_type": "charge.completed",
            "transaction_id": "chg_TAP_TEST_001",
            "external_reference": "DITSL_TAP_TEST_001",
            "user_id": null,
            "wallet_id": 5,
            "amount": "100.00",
            "currency": "BDT",
            "status": "completed",
            "tap_status": "CAPTURED",
            "payment_method": null,
            "card_last4": null,
            "description": null,
            "metadata": null,
            "error_code": null,
            "error_message": null,
            "processed_by": "DITSL",
            "processed_at": "2026-03-04 01:15:30",
            "raw_payload": "{...}",
            "created_at": "2026-03-04 01:15:30",
            "updated_at": "2026-03-04 01:15:30"
        },
        "wallet": {
            "id": 5,
            "wallet_number": "880171064440",
            "name": "Test User",
            "balance": "100.00",
            "currency": "BDT",
            "status": "active",
            "created_at": "2026-03-01 10:00:00",
            "updated_at": "2026-03-04 01:15:30"
        }
    }
}
```

### Response - Not Found (404)
```json
{
    "statusCode": 404,
    "message": "Transaction not found",
    "data": null
}
```

### Response - Missing Parameters (400)
```json
{
    "statusCode": 400,
    "message": "Either transaction_id or external_reference is required",
    "data": null
}
```

### Response - Invalid Signature (401)
```json
{
    "statusCode": 401,
    "message": "Invalid webhook signature",
    "data": null
}
```

---

## 2. Reconciliation API

### Endpoint
```
POST /api/v1/tap/reconciliation
```

### Purpose
Retrieve bulk transaction data for a specific date range to perform reconciliation between TAP and DITSL systems.

### Authentication
- **Header:** `X-Tap-Signature` or `X-Webhook-Signature`
- **Method:** HMAC SHA256
- **Secret:** `99E9418B-6CBD-4574-A646-6415C062DEC7`

### Request Headers
```
Content-Type: application/json
X-Tap-Signature: <HMAC_SHA256_signature>
```

### Request Body
```json
{
    "from_date": "2026-03-01T00:00:00Z",
    "to_date": "2026-03-04T23:59:59Z",
    "status": "completed",
    "limit": 100,
    "offset": 0
}
```

**Parameters:**
- `from_date` (string, **required**): Start date in ISO 8601 format (e.g., `2026-03-01T00:00:00Z`)
- `to_date` (string, **required**): End date in ISO 8601 format (e.g., `2026-03-04T23:59:59Z`)
- `status` (string, optional): Filter by status (`completed`, `pending`, `failed`)
- `limit` (integer, optional): Number of records per page (default: 100, max: 1000)
- `offset` (integer, optional): Pagination offset (default: 0)

### Response - Success (200)
```json
{
    "statusCode": 200,
    "message": "Reconciliation data retrieved successfully",
    "data": {
        "summary": {
            "total_records": 150,
            "total_amount": "15000.00",
            "currency": "BDT",
            "from_date": "2026-03-01 00:00:00",
            "to_date": "2026-03-04 23:59:59",
            "status_filter": "completed",
            "limit": 100,
            "offset": 0
        },
        "transactions": [
            {
                "id": 1,
                "event_id": "DITSL_TAP_TEST_001",
                "transaction_id": "chg_TAP_TEST_001",
                "external_reference": "DITSL_TAP_TEST_001",
                "wallet_id": 5,
                "wallet_number": "880171064440",
                "amount": "100.00",
                "currency": "BDT",
                "status": "completed",
                "tap_status": "CAPTURED",
                "processed_by": "DITSL",
                "processed_at": "2026-03-04 01:15:30",
                "created_at": "2026-03-04 01:15:30"
            },
            {
                "id": 2,
                "event_id": "DITSL_TAP_TEST_002",
                "transaction_id": "chg_TAP_TEST_002",
                "external_reference": "DITSL_TAP_TEST_002",
                "wallet_id": 6,
                "wallet_number": "880171064441",
                "amount": "200.00",
                "currency": "BDT",
                "status": "completed",
                "tap_status": "CAPTURED",
                "processed_by": "DITSL",
                "processed_at": "2026-03-04 02:30:15",
                "created_at": "2026-03-04 02:30:15"
            }
        ]
    }
}
```

### Response - No Data Found (404)
```json
{
    "statusCode": 404,
    "message": "No transactions found for the specified date range",
    "data": null
}
```

### Response - Missing Date Range (400)
```json
{
    "statusCode": 400,
    "message": "Both from_date and to_date are required (ISO 8601 format)",
    "data": null
}
```

### Response - Invalid Date Format (400)
```json
{
    "statusCode": 400,
    "message": "Invalid date format. Use ISO 8601 format (e.g., 2026-03-01T00:00:00Z)",
    "data": null
}
```

### Response - Invalid Signature (401)
```json
{
    "statusCode": 401,
    "message": "Invalid webhook signature",
    "data": null
}
```

---

## 3. HMAC Signature Generation

Both endpoints require HMAC SHA256 signature for authentication.

### Signature Algorithm
```
HMAC-SHA256(request_body, webhook_secret)
```

### JavaScript Example (Postman Pre-request Script)
```javascript
// Webhook secret
const webhookSecret = "99E9418B-6CBD-4574-A646-6415C062DEC7";

// Get request body
const requestBody = pm.request.body.raw;

// Generate HMAC signature
const signature = CryptoJS.HmacSHA256(requestBody, webhookSecret).toString();

// Add signature to headers
pm.request.headers.upsert({
    key: 'X-Tap-Signature',
    value: signature
});

console.log("Generated Signature:", signature);
```

### PHP Example
```php
$webhookSecret = '99E9418B-6CBD-4574-A646-6415C062DEC7';
$requestBody = json_encode($payload);
$signature = hash_hmac('sha256', $requestBody, $webhookSecret);

// Add to request headers
$headers = [
    'Content-Type: application/json',
    'X-Tap-Signature: ' . $signature
];
```

### Python Example
```python
import hmac
import hashlib
import json

webhook_secret = '99E9418B-6CBD-4574-A646-6415C062DEC7'
request_body = json.dumps(payload)
signature = hmac.new(
    webhook_secret.encode('utf-8'),
    request_body.encode('utf-8'),
    hashlib.sha256
).hexdigest()

headers = {
    'Content-Type': 'application/json',
    'X-Tap-Signature': signature
}
```

---

## 4. Complete Test Examples

### 4.1 Transaction Enquiry - cURL
```bash
#!/bin/bash

# Configuration
BASE_URL="http://localhost:8080"
WEBHOOK_SECRET="99E9418B-6CBD-4574-A646-6415C062DEC7"

# Request payload
PAYLOAD='{
    "transaction_id": "chg_TAP_TEST_001",
    "external_reference": "DITSL_TAP_TEST_001"
}'

# Generate HMAC signature
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac "$WEBHOOK_SECRET" | awk '{print $2}')

# Send request
curl -X POST "$BASE_URL/api/v1/tap/transaction-enquiry" \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD"
```

### 4.2 Reconciliation - cURL
```bash
#!/bin/bash

# Configuration
BASE_URL="http://localhost:8080"
WEBHOOK_SECRET="99E9418B-6CBD-4574-A646-6415C062DEC7"

# Request payload
PAYLOAD='{
    "from_date": "2026-03-01T00:00:00Z",
    "to_date": "2026-03-04T23:59:59Z",
    "status": "completed",
    "limit": 100,
    "offset": 0
}'

# Generate HMAC signature
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac "$WEBHOOK_SECRET" | awk '{print $2}')

# Send request
curl -X POST "$BASE_URL/api/v1/tap/reconciliation" \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD"
```

---

## 5. Postman Collection Setup

### Collection Variables
```
base_url: http://localhost:8080
tap_secret: 99E9418B-6CBD-4574-A646-6415C062DEC7
```

### Pre-request Script (Collection Level)
```javascript
// Generate HMAC signature for each request
const webhookSecret = pm.collectionVariables.get("tap_secret");
const requestBody = pm.request.body.raw;

if (requestBody) {
    const signature = CryptoJS.HmacSHA256(requestBody, webhookSecret).toString();
    pm.request.headers.upsert({
        key: 'X-Tap-Signature',
        value: signature
    });
    console.log("✅ HMAC Signature generated:", signature);
}
```

### Request 1: Transaction Enquiry
```
POST {{base_url}}/api/v1/tap/transaction-enquiry

Body (JSON):
{
    "transaction_id": "chg_TAP_TEST_001"
}
```

### Request 2: Reconciliation
```
POST {{base_url}}/api/v1/tap/reconciliation

Body (JSON):
{
    "from_date": "2026-03-01T00:00:00Z",
    "to_date": "2026-03-04T23:59:59Z",
    "status": "completed",
    "limit": 100
}
```

---

## 6. Error Handling

### Common Error Responses

| Status Code | Message | Cause | Solution |
|-------------|---------|-------|----------|
| 400 | Missing required fields | Required parameters not provided | Include transaction_id or external_reference |
| 400 | Invalid date format | Date not in ISO 8601 format | Use format: `2026-03-01T00:00:00Z` |
| 401 | Invalid webhook signature | HMAC signature mismatch | Regenerate signature with correct secret |
| 401 | Missing webhook signature | No X-Tap-Signature header | Add HMAC signature to request headers |
| 404 | Transaction not found | Transaction doesn't exist in database | Verify transaction ID is correct |
| 404 | No transactions found | No data for date range | Adjust date range or status filter |
| 500 | Internal server error | Server-side error | Check server logs, contact support |

---

## 7. Database Schema

### Table: `tap_deposits`

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `event_id` | VARCHAR(255) | TAP event ID (unique) |
| `event_type` | VARCHAR(100) | Event type (e.g., charge.completed) |
| `transaction_id` | VARCHAR(255) | TAP charge ID |
| `external_reference` | VARCHAR(255) | External reference ID |
| `user_id` | INT | User ID (if available) |
| `wallet_id` | INT | Wallet ID (foreign key) |
| `amount` | DECIMAL(15,2) | Transaction amount |
| `currency` | VARCHAR(3) | Currency code (BDT, USD, etc.) |
| `status` | VARCHAR(50) | Processing status (completed, pending, failed) |
| `tap_status` | VARCHAR(50) | TAP payment status (CAPTURED, etc.) |
| `payment_method` | VARCHAR(50) | Payment method used |
| `card_last4` | VARCHAR(4) | Last 4 digits of card |
| `description` | TEXT | Transaction description |
| `metadata` | JSON | Additional metadata |
| `error_code` | VARCHAR(100) | Error code (if failed) |
| `error_message` | TEXT | Error message (if failed) |
| `processed_by` | VARCHAR(50) | Processor name (DITSL) |
| `processed_at` | DATETIME | Processing timestamp |
| `raw_payload` | TEXT | Full webhook payload |
| `created_at` | DATETIME | Record creation time |
| `updated_at` | DATETIME | Last update time |

**Indexes:**
- `event_id` (UNIQUE)
- `transaction_id` (INDEX)
- `external_reference` (INDEX)
- `wallet_id` (INDEX)
- `status` (INDEX)
- `created_at` (INDEX)

---

## 8. API Audit Logging

Both endpoints log detailed information for audit purposes in the `tap_api_audit` table.

### Logged Information
- Endpoint name
- Request method and IP address
- User agent
- Request payload
- Response status and message
- Transaction identifiers
- Execution time
- Error messages (if any)

### Example Audit Record
```json
{
    "endpoint": "transaction-enquiry",
    "request_method": "POST",
    "request_ip": "192.168.1.100",
    "transaction_id": "chg_TAP_TEST_001",
    "response_status": 200,
    "response_message": "Transaction found",
    "records_returned": 1,
    "execution_time": 0.025,
    "created_at": "2026-03-04 01:15:30"
}
```

---

## 9. Performance & Limits

### Transaction Enquiry
- **Response Time:** < 100ms (average)
- **Rate Limit:** Not implemented (recommended: 100 requests/minute)
- **Timeout:** 30 seconds

### Reconciliation
- **Response Time:** < 500ms (average)
- **Max Records per Request:** 1000
- **Default Limit:** 100
- **Rate Limit:** Not implemented (recommended: 10 requests/minute)
- **Timeout:** 60 seconds

### Recommendations
- Use pagination for large datasets
- Implement caching on TAP side for frequently queried transactions
- Schedule reconciliation during off-peak hours
- Use status filters to reduce response size

---

## 10. Security Considerations

### ✅ Implemented Security
1. **HMAC Signature Verification** - All requests verified with SHA256 HMAC
2. **Constant-Time Comparison** - Using `hash_equals()` to prevent timing attacks
3. **Input Validation** - All parameters validated before processing
4. **SQL Injection Protection** - Using CodeIgniter Query Builder
5. **Audit Logging** - Complete request/response logging

### 🔐 Additional Recommendations
1. **IP Whitelisting** - Restrict access to TAP IP addresses only
2. **Rate Limiting** - Implement request throttling
3. **HTTPS Only** - Enforce SSL/TLS in production
4. **API Key Authentication** - Add secondary authentication layer
5. **Request Signing** - Include timestamp in HMAC calculation

---

## 11. Testing Checklist

### Transaction Enquiry Tests
- [ ] Query by transaction_id (success)
- [ ] Query by external_reference (success)
- [ ] Query with both identifiers (success)
- [ ] Query with no identifiers (400 error)
- [ ] Query non-existent transaction (404 error)
- [ ] Query with invalid HMAC signature (401 error)
- [ ] Query with missing signature header (401 error)
- [ ] Verify response includes wallet details
- [ ] Verify audit log entry created

### Reconciliation Tests
- [ ] Query with valid date range (success)
- [ ] Query with status filter (success)
- [ ] Query with pagination (limit/offset)
- [ ] Query with missing from_date (400 error)
- [ ] Query with missing to_date (400 error)
- [ ] Query with invalid date format (400 error)
- [ ] Query with invalid HMAC signature (401 error)
- [ ] Query date range with no data (404 error)
- [ ] Verify summary totals are correct
- [ ] Verify audit log entry created
- [ ] Test with 1000 records (max limit)
- [ ] Test pagination (offset increments)

---

## 12. Troubleshooting

### Problem: 401 Invalid Signature
**Cause:** HMAC signature doesn't match

**Solutions:**
1. Verify webhook secret: `99E9418B-6CBD-4574-A646-6415C062DEC7`
2. Check request body is exactly what was signed (no whitespace changes)
3. Ensure Content-Type is `application/json`
4. Verify signature is in `X-Tap-Signature` header
5. Check signature generation algorithm (HMAC-SHA256)

### Problem: 404 Transaction Not Found
**Cause:** Transaction doesn't exist in database

**Solutions:**
1. Verify transaction_id or external_reference is correct
2. Check if deposit webhook was received first
3. Query database directly: 
   ```sql
   SELECT * FROM tap_deposits WHERE transaction_id = 'chg_TAP_TEST_001';
   ```
4. Check audit logs for webhook receipt

### Problem: 400 Invalid Date Format
**Cause:** Date not in ISO 8601 format

**Solutions:**
1. Use format: `2026-03-01T00:00:00Z`
2. Include timezone (Z for UTC)
3. Avoid formats like `2026-03-01` or `03/01/2026`

### Problem: No Data in Reconciliation
**Cause:** No transactions in specified date range

**Solutions:**
1. Expand date range
2. Remove status filter
3. Check if any deposits exist in database
4. Verify deposits were processed during that period

---

## 13. Production Deployment

### Environment Variables
```bash
# .env file
TAP_WEBHOOK_SECRET=99E9418B-6CBD-4574-A646-6415C062DEC7
CI_ENVIRONMENT=production
```

### Server Requirements
- PHP 8.0+
- CodeIgniter 4.4.3+
- MySQL 8.0+
- HTTPS/SSL certificate
- Sufficient memory for large reconciliation queries

### Pre-deployment Checklist
- [ ] Test all endpoints in staging
- [ ] Verify HMAC signature verification
- [ ] Check database indexes are created
- [ ] Enable HTTPS/SSL
- [ ] Configure rate limiting
- [ ] Set up monitoring and alerts
- [ ] Test error scenarios
- [ ] Verify audit logging is working
- [ ] Document API credentials
- [ ] Set up backup procedures

---

## 14. Support & Contact

### Documentation
- Integration Guide: `TAP_INTEGRATION_SUMMARY.md`
- Webhook Guide: `TAP_WEBHOOK_INTEGRATION_GUIDE.md`
- API Reference: This document

### Technical Support
- **Email:** support@ditsl.com
- **Response Time:** 24 hours
- **Escalation:** critical@ditsl.com

### Monitoring
- Check server logs: `writable/logs/log-YYYY-MM-DD.log`
- Query audit table: `SELECT * FROM tap_api_audit ORDER BY created_at DESC LIMIT 50`

---

## 15. Changelog

### Version 1.0.0 (2026-03-04)
- ✅ Initial implementation
- ✅ Transaction enquiry endpoint
- ✅ Reconciliation endpoint
- ✅ HMAC signature verification
- ✅ Audit logging
- ✅ Error handling
- ✅ Pagination support
- ✅ Status filtering

---

## Quick Reference

### Endpoints
```
POST /api/v1/tap/transaction-enquiry
POST /api/v1/tap/reconciliation
```

### Authentication
```
Header: X-Tap-Signature
Value: HMAC-SHA256(request_body, "99E9418B-6CBD-4574-A646-6415C062DEC7")
```

### Status Codes
- `200` - Success
- `400` - Bad Request (invalid parameters)
- `401` - Unauthorized (invalid signature)
- `404` - Not Found (no data)
- `500` - Internal Server Error

### Date Format
```
ISO 8601: YYYY-MM-DDTHH:MM:SSZ
Example: 2026-03-01T00:00:00Z
```

---

**Document Version:** 1.0.0  
**Last Updated:** March 4, 2026  
**Maintained By:** DITSL Development Team
