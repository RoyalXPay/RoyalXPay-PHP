# TAP Payment Integration - Complete Webhook System

## Overview
Complete webhook integration for TAP payment system with three endpoints:
1. **Deposit Webhook** - Receives deposit notifications
2. **Balance Update Webhook** - TAP triggers periodic balance sync
3. **Get Balance** - TAP queries current wallet balance

## 🔐 Authentication
**Method:** HMAC-SHA256 signature verification  
**Secret:** `TAP_WEBHOOK_SECRET = '99E9418B-6CBD-4574-A646-6415C062DEC7'`  
**Header:** `X-Tap-Signature`

---

## 📍 API Endpoints

### 1. Deposit Webhook
**Endpoint:** `POST /api/tap/deposit-webhook`

**Purpose:** Receives deposit notifications from TAP when payments are completed

**Request:**
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

**Response:**
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
    "processed_by": "DITSL",
    "current_balance": 600.50
  }
}
```

**Features:**
- ✅ Automatic wallet balance update
- ✅ Idempotency (prevents duplicate processing)
- ✅ Returns current balance after deposit
- ✅ Stores audit trail in database

---

### 2. Balance Update Webhook
**Endpoint:** `POST /api/tap/balance-update`

**Purpose:** TAP system can continuously check and trigger this webhook to update balance periodically

**Use Cases:**
- TAP detects balance changes in their system
- Periodic balance reconciliation (triggered on schedule)
- On-demand balance sync from TAP dashboard
- Automated triggers when discrepancies detected

**Request:**
```json
{
  "wallet_number": "880171064443",
  "balance": 750.00,
  "currency": "GBP",
  "timestamp": "2026-02-28T11:30:00+00:00",
  "reference_id": "BAL_UPDATE_001"
}
```

**Response:**
```json
{
  "statusCode": 200,
  "message": "Balance updated successfully",
  "data": {
    "event_id": "BAL_UPDATE_001",
    "event_type": "wallet.balance.updated",
    "timestamp": "2026-02-28T11:30:00+00:00",
    "wallet_number": "880171064443",
    "old_balance": 600.50,
    "new_balance": 750.00,
    "currency": "GBP",
    "status": "completed",
    "processed_by": "DITSL"
  }
}
```

**Features:**
- ✅ Direct balance synchronization
- ✅ Shows old and new balance
- ✅ Reduces webhook exhaustion (proactive updates)
- ✅ Timestamp tracking for audit

---

### 3. Get Balance
**Endpoint:** `POST /api/tap/get-balance`

**Purpose:** TAP can query current wallet balance anytime (before/after transactions)

**Use Cases:**
- Pre-transaction balance check
- Post-transaction verification
- Balance inquiry for customer support
- Real-time balance monitoring

**Request:**
```json
{
  "wallet_number": "880171064443"
}
```

**Response:**
```json
{
  "statusCode": 200,
  "message": "Balance retrieved successfully",
  "data": {
    "wallet_number": "880171064443",
    "wallet_id": 1,
    "balance": 750.00,
    "currency": "GBP",
    "status": "active",
    "timestamp": "2026-02-28T11:35:00+00:00",
    "processed_by": "DITSL"
  }
}
```

**Features:**
- ✅ Real-time balance query
- ✅ No balance update (read-only)
- ✅ Wallet status included
- ✅ Fast response for TAP system

---

## 🔄 Integration Workflows

### Workflow 1: Deposit with Balance in Response
```
Customer Makes Payment
        ↓
TAP Processes Payment
        ↓
TAP → POST /api/tap/deposit-webhook
        ↓
RoyalXPay Updates Wallet Balance
        ↓
Response Includes current_balance
        ↓
TAP Knows Balance Without Extra Call
```

**Benefit:** Reduces webhook calls - balance included in deposit response

---

### Workflow 2: Periodic Balance Sync (Scheduled)
```
TAP Scheduler (Every 5 minutes)
        ↓
TAP Checks Balance in Their System
        ↓
TAP → POST /api/tap/balance-update
        ↓
RoyalXPay Syncs Balance
        ↓
Balances Stay in Sync
```

**Benefit:** Proactive balance synchronization prevents drift

---

### Workflow 3: On-Demand Balance Check
```
TAP Needs Current Balance
        ↓
TAP → POST /api/tap/get-balance
        ↓
RoyalXPay Returns Current Balance
        ↓
TAP Uses Balance for Validation
```

**Benefit:** Real-time balance for business logic decisions

---

### Workflow 4: Balance Change Detection
```
TAP Detects Balance Mismatch
        ↓
TAP → POST /api/tap/balance-update
        ↓
RoyalXPay Updates to Match TAP
        ↓
Balance Reconciled Automatically
```

**Benefit:** Automatic reconciliation when discrepancies found

---

## 🔐 Security

### HMAC Signature Verification
All three endpoints verify HMAC signature:

```php
$signature = hash_hmac('sha256', $rawPayload, TAP_WEBHOOK_SECRET);
```

**Header:** `X-Tap-Signature: <calculated_signature>`

### Configuration
```bash
# .env file
TAP_WEBHOOK_SECRET=99E9418B-6CBD-4574-A646-6415C062DEC7
```

---

## 📊 Database Schema

### Table: tap_deposits
Stores all deposit events:
- event_id (unique DITSL ID)
- external_reference (TAP event ID)
- transaction_id, amount, currency
- wallet_id, wallet_number
- status (completed/failed/pending)
- raw_payload (audit trail)

### Table: remittance_wallets
Stores wallet balances:
- id, wallet_number, balance
- currency, status, updated_at

---

## 🧪 Testing

### Run All Tests
```bash
./test_tap_deposit.sh
```

### Manual Tests

**Test Deposit:**
```bash
curl -X POST http://localhost:8080/api/tap/deposit-webhook \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: test" \
  -d '{"id":"evt_001","event":"charge.succeeded","object":{"id":"chg_001","amount":100,"currency":"GBP","status":"CAPTURED","customer":{"phone":{"number":"880171064443"}}}}'
```

**Test Balance Update:**
```bash
curl -X POST http://localhost:8080/api/tap/balance-update \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: test" \
  -d '{"wallet_number":"880171064443","balance":500,"currency":"GBP"}'
```

**Test Get Balance:**
```bash
curl -X POST http://localhost:8080/api/tap/get-balance \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: test" \
  -d '{"wallet_number":"880171064443"}'
```

---

## 📈 Monitoring

### Check Logs
```bash
tail -f writable/logs/log-*.log | grep "TAP"
```

### Check Database
```sql
-- Recent deposits
SELECT * FROM tap_deposits ORDER BY created_at DESC LIMIT 10;

-- Wallet balances
SELECT wallet_number, balance, currency, updated_at 
FROM remittance_wallets 
ORDER BY updated_at DESC;
```

### Statistics
```sql
-- Deposit summary
SELECT 
    status,
    COUNT(*) as count,
    SUM(amount) as total_amount
FROM tap_deposits
WHERE DATE(created_at) = CURDATE()
GROUP BY status;
```

---

## 🚀 Production Deployment

### TAP Dashboard Configuration

**1. Deposit Webhook:**
- URL: `https://stg-gw.tadlbd.com:885/api/tap/deposit-webhook`
- Event: `charge.succeeded`
- Secret: `99E9418B-6CBD-4574-A646-6415C062DEC7`

**2. Balance Update (TAP Trigger):**
- URL: `https://stg-gw.tadlbd.com:885/api/tap/balance-update`
- Trigger: Scheduled (every 5 minutes) or on-demand
- Secret: Same as above

**3. Get Balance (TAP Query):**
- URL: `https://stg-gw.tadlbd.com:885/api/tap/get-balance`
- Usage: Call anytime to get current balance
- Secret: Same as above

### Environment Setup
```bash
# .env (production)
TAP_WEBHOOK_SECRET=99E9418B-6CBD-4574-A646-6415C062DEC7
app.baseURL = 'https://stg-gw.tadlbd.com:885/'
```

---

## ✅ Benefits Summary

### Reduced Webhook Exhaustion
- ✅ Deposit response includes `current_balance`
- ✅ TAP doesn't need separate balance query after deposit
- ✅ Single transaction = single webhook call

### Proactive Balance Sync
- ✅ TAP can trigger periodic updates
- ✅ Prevents balance drift
- ✅ Automatic reconciliation

### Real-Time Balance Query
- ✅ On-demand balance checks
- ✅ No unnecessary updates
- ✅ Fast read-only queries

---

## 📞 Support

### Error Responses

**400 - Invalid Payload**
```json
{"statusCode": 400, "message": "Invalid payload structure", "data": null}
```

**401 - Auth Failed**
```json
{"statusCode": 401, "message": "Authentication failed", "data": null}
```

**500 - Server Error**
```json
{"statusCode": 500, "message": "Internal server error", "data": null}
```

### Troubleshooting
1. Check HMAC signature matches
2. Verify wallet exists in database
3. Review logs for detailed errors
4. Test with curl commands first

---

## 📅 Changelog

### Version 1.1.0 (2026-02-28)
- ✅ Added balance update webhook
- ✅ Added get balance endpoint
- ✅ Deposit response now includes current_balance
- ✅ Support for periodic balance synchronization
- ✅ Reduced webhook exhaustion

### Version 1.0.0 (2026-02-28)
- ✅ Initial deposit webhook implementation
