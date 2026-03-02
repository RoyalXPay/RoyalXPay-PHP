# TAP Payment Webhook Integration - DITSL

## Overview
Single-endpoint webhook integration for TAP payment deposits. This system receives deposit notifications from TAP, validates them, processes deposits, and returns a standardized response.

**Key Points:**
- ✅ Single endpoint: `POST /api/tap/deposit-webhook`
- ✅ DITSL and our platform are the same system
- ✅ HMAC signature verification for security
- ✅ Idempotency to prevent duplicate processing
- ✅ Automatic wallet balance updates
- ✅ Full audit trail with raw payload storage

---

## Architecture

```
TAP Payment Gateway
        ↓
   [HTTPS POST]
        ↓
DITSL Webhook Endpoint
        ↓
   [Validate & Process]
        ↓
  Update Wallet Balance
        ↓
Standardized JSON Response
```

---

## API Endpoint

### **POST** `/api/tap/deposit-webhook`

**Purpose:** Receive deposit notifications from TAP

**Authentication:** HMAC signature verification (no API key required)

**Headers:**
```
Content-Type: application/json
X-Tap-Signature: <hmac_sha256_signature>
```

---

## Request Payload (from TAP)

```json
{
  "id": "evt_tap_12345",
  "event": "charge.succeeded",
  "object": {
    "id": "chg_abc123",
    "amount": 100.50,
    "currency": "GBP",
    "status": "CAPTURED",
    "customer": {
      "phone": {
        "number": "880171064443"
      }
    },
    "metadata": {
      "user_id": "USER_001"
    }
  }
}
```

**Required Fields:**
- `id` - TAP event ID
- `event` or `event_type` - Event type
- `object.id` - Transaction ID
- `object.amount` - Deposit amount
- `object.currency` - Currency code
- `object.status` - Payment status (CAPTURED = completed)
- `object.customer.phone.number` - Wallet phone number

---

## Response Payload (Standardized)

### Success (200 OK)
```json
{
  "statusCode": 200,
  "message": "Deposit processed successfully",
  "data": {
    "event_id": "DITSL_1709115600_abc123",
    "event_type": "wallet.deposit.completed",
    "timestamp": "2026-02-28T11:20:00+00:00",
    "transaction_id": "chg_abc123",
    "external_reference": "evt_tap_12345",
    "user_id": "USER_001",
    "amount": 100.50,
    "currency": "GBP",
    "status": "completed",
    "processed_by": "DITSL"
  }
}
```

### Duplicate Event (200 OK)
```json
{
  "statusCode": 200,
  "message": "Event already processed",
  "data": {
    "event_id": "evt_tap_12345",
    "status": "duplicate"
  }
}
```

### Invalid Payload (400)
```json
{
  "statusCode": 400,
  "message": "Invalid payload structure",
  "data": null
}
```

### Authentication Failed (401)
```json
{
  "statusCode": 401,
  "message": "Authentication failed",
  "data": null
}
```

### Wallet Not Found (400)
```json
{
  "statusCode": 400,
  "message": "Wallet not found",
  "data": null
}
```

### Server Error (500)
```json
{
  "statusCode": 500,
  "message": "Internal server error",
  "data": null
}
```

---

## Security

### HMAC Signature Verification

**Algorithm:** HMAC-SHA256

**Header:** `X-Tap-Signature` or `X-Webhook-Signature`

**Verification Process:**
```php
$rawPayload = $request->getBody(); // Raw JSON string
$webhookSecret = env('TAP_WEBHOOK_SECRET');
$expectedSignature = hash_hmac('sha256', $rawPayload, $webhookSecret);

if (hash_equals($expectedSignature, $receivedSignature)) {
    // Valid signature
}
```

**Configuration:**
- Set `TAP_WEBHOOK_SECRET` in `.env` file
- Get secret from TAP Dashboard > Developers > Webhooks
- If not configured, verification is skipped (development only)

---

## Idempotency

**Purpose:** Prevent duplicate processing if TAP retries webhook

**Mechanism:**
- Uses TAP's `external_reference` (event ID) as unique key
- Database has unique constraint on `external_reference`
- Before processing, checks if event already exists
- Returns 200 OK for duplicates without reprocessing

**Database Check:**
```sql
SELECT * FROM tap_deposits 
WHERE external_reference = 'evt_tap_12345' 
OR event_id = 'evt_tap_12345'
```

---

## Wallet Mapping

**Key Field:** `customer.phone.number` from TAP payload

**Process:**
1. Extract phone number from TAP webhook
2. Query `remittance_wallets` table: `WHERE wallet_number = phone`
3. If found: Update balance
4. If not found: Return 400 error and log failure

**Important:** Ensure phone numbers in your database match TAP's format (with country code).

---

## Database Schema

### Table: `tap_deposits`

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| event_id | VARCHAR(255) | Unique DITSL event ID |
| event_type | VARCHAR(100) | wallet.deposit.completed/failed/pending |
| transaction_id | VARCHAR(255) | TAP transaction reference |
| external_reference | VARCHAR(255) | TAP event ID (unique) |
| user_id | VARCHAR(100) | User identifier |
| wallet_id | INT | Internal wallet ID |
| wallet_number | VARCHAR(50) | Phone number |
| amount | DECIMAL(15,2) | Deposit amount |
| currency | VARCHAR(10) | Currency code |
| status | ENUM | completed/failed/pending |
| error_message | TEXT | Error details if failed |
| processed_by | VARCHAR(50) | Always 'DITSL' |
| raw_payload | LONGTEXT | Raw TAP JSON for audit |
| processed_at | DATETIME | ISO 8601 timestamp |
| created_at | DATETIME | When received |
| updated_at | DATETIME | Last update |

**Indexes:**
- Primary: `id`
- Unique: `event_id`, `external_reference`
- Index: `wallet_id`, `status`, `created_at`

---

## Configuration

### Environment Variables (.env)

```bash
# TAP Webhook Configuration
TAP_WEBHOOK_SECRET=your_tap_webhook_secret_here
```

**Get Webhook Secret:**
1. Login to TAP Dashboard: https://dashboard.tap.company
2. Navigate to: Developers > Webhooks
3. Create/Edit webhook endpoint
4. Copy the webhook secret
5. Paste into `.env` file

---

## Testing

### Local Testing

```bash
# Run test script
./test_tap_deposit.sh
```

### Manual Testing with curl

```bash
curl -X POST http://localhost:8080/api/tap/deposit-webhook \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: test_signature" \
  -d '{
    "id": "evt_test_001",
    "event": "charge.succeeded",
    "object": {
      "id": "chg_test_123",
      "amount": 100.50,
      "currency": "GBP",
      "status": "CAPTURED",
      "customer": {
        "phone": {
          "number": "880171064443"
        }
      }
    }
  }'
```

### Verify Results

1. **Check Database:**
```sql
SELECT * FROM tap_deposits ORDER BY created_at DESC LIMIT 10;
SELECT * FROM remittance_wallets WHERE wallet_number = '880171064443';
```

2. **Check Logs:**
```bash
tail -f writable/logs/log-*.log | grep "TAP Webhook"
```

---

## TAP Dashboard Configuration

### Setup Webhook in TAP

1. Login to TAP Dashboard
2. Go to: **Developers > Webhooks**
3. Click **Add Webhook Endpoint**
4. **URL:** `https://yourdomain.com/api/tap/deposit-webhook`
5. **Events:** Select `charge.succeeded`
6. **Save** and copy the webhook secret
7. Update `.env` with the secret

### Test from TAP Dashboard

1. Use "Send Test Webhook" button
2. Select `charge.succeeded` event
3. Send test payload
4. Verify response is 200 OK

---

## Production Deployment

### Pre-Deployment Checklist

- [ ] Update `.env` with production `TAP_WEBHOOK_SECRET`
- [ ] Ensure HTTPS is enabled (TAP requires HTTPS)
- [ ] Valid SSL certificate installed
- [ ] Webhook URL is publicly accessible
- [ ] Database migration completed
- [ ] Test wallets exist in database
- [ ] Firewall allows TAP IP addresses (optional)

### Deployment Steps

1. **Update .env:**
```bash
TAP_WEBHOOK_SECRET=prod_secret_from_tap_dashboard
```

2. **Configure TAP:**
   - Webhook URL: `https://stg-gw.tadlbd.com:885/api/tap/deposit-webhook`
   - Event: `charge.succeeded`
   - Save webhook secret

3. **Test:**
   - Use TAP's test webhook feature
   - Verify response and database updates
   - Check logs for any errors

4. **Monitor:**
   - Watch for failed deposits in `tap_deposits` table
   - Set up alerts for status = 'failed'
   - Review logs daily

---

## Error Handling & Retry

### HTTP Status Codes

| Code | Meaning | TAP Action |
|------|---------|------------|
| 200 | Success or duplicate | No retry |
| 400 | Invalid payload or wallet not found | No retry |
| 401 | Invalid HMAC signature | No retry |
| 500 | Server error | Retry with exponential backoff |

### TAP Retry Behavior

- TAP automatically retries failed webhooks (non-200 responses)
- Uses exponential backoff strategy
- Maximum attempts: Configured by TAP (typically 5-10)
- Retry intervals: 1min, 5min, 15min, 1hr, etc.

### Our Idempotency Handling

- All retries checked against `external_reference`
- Duplicate events return 200 OK immediately
- No duplicate processing or wallet updates
- Original event data returned

---

## Monitoring & Maintenance

### Daily Checks

```sql
-- Check failed deposits
SELECT * FROM tap_deposits WHERE status = 'failed' ORDER BY created_at DESC;

-- Check deposit statistics
SELECT 
    status, 
    COUNT(*) as count, 
    SUM(amount) as total_amount,
    currency
FROM tap_deposits 
WHERE DATE(created_at) = CURDATE()
GROUP BY status, currency;
```

### Log Monitoring

```bash
# Watch for errors
tail -f writable/logs/log-*.log | grep "error"

# Watch TAP webhook activity
tail -f writable/logs/log-*.log | grep "TAP Webhook"
```

### Data Retention

- Keep completed deposits: 90 days minimum
- Keep failed deposits: Indefinitely for investigation
- Archive old raw_payload data after 1 year

---

## Troubleshooting

### Webhook Not Received

1. Check TAP Dashboard webhook logs
2. Verify webhook URL is correct and accessible
3. Test URL with curl from external machine
4. Check firewall/security groups
5. Verify SSL certificate is valid

### Signature Verification Failed

1. Verify `TAP_WEBHOOK_SECRET` matches TAP Dashboard
2. Check webhook secret hasn't expired/changed
3. Ensure raw payload is used (not parsed JSON)
4. Verify header name: `X-Tap-Signature`

### Wallet Not Found

1. Check phone number format in database
2. Verify TAP sends phone with country code
3. Ensure wallet exists before deposit
4. Review wallet creation process

### Duplicate Processing

1. Check unique constraints on database table
2. Verify idempotency logic is working
3. Review logs for `event_id` comparison
4. Test with same payload twice

---

## Compliance & Audit

### Audit Trail

✅ Every webhook stored in `tap_deposits`  
✅ Raw TAP payload preserved in `raw_payload` field  
✅ Processing timestamp recorded  
✅ Error messages captured  
✅ Processed by 'DITSL' indicator  

### Data Protection

- Raw payloads contain sensitive customer data
- Ensure database is encrypted at rest
- Restrict access to `tap_deposits` table
- Regular backups with encryption
- GDPR/PCI compliance considerations

---

## Support & Contacts

### TAP Support
- Documentation: https://developers.tap.company/docs
- Webhook Guide: https://developers.tap.company/docs/webhooks
- Support Email: support@tap.company

### Internal Escalation
- Database Issues: DBA Team
- Server/SSL Issues: DevOps Team
- Business Logic: Technical Lead

---

## Changelog

### Version 1.0.0 (2026-02-28)
- ✅ Single endpoint webhook integration
- ✅ HMAC signature verification
- ✅ Idempotency handling
- ✅ Automatic wallet balance updates
- ✅ Standardized response format
- ✅ Full audit logging
- ✅ Error handling and retry logic
