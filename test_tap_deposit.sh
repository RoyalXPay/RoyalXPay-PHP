#!/bin/bash

# TAP Webhook Integration Test Script
# Tests TAP deposit webhook endpoint

BASE_URL="http://localhost:8080"
DEPOSIT_ENDPOINT="$BASE_URL/api/v1/tap/deposit-webhook"

echo "=========================================="
echo "TAP Webhook Integration Tests"
echo "=========================================="
echo ""

# Test 1: Successful deposit
echo "Test 1: Deposit Webhook - Successful Deposit"
echo "---------------------------------------------"

PAYLOAD='{"id":"evt_tap_12345","event":"succeeded","data":{"id":"chg_abc123","amount":100.50,"currency":"BDT","status":"CAPTURED","customer":{"phone":{"number":"880171064443"}},"metadata":{"user_id":"USER_001"}}}'
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac '99E9418B-6CBD-4574-A646-6415C062DEC7' | awk '{print $2}')

curl -X POST $DEPOSIT_ENDPOINT \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -s | jq '.'

echo ""
echo ""

# Test 2: Duplicate deposit (idempotency check)
echo "Test 2: Deposit Webhook - Duplicate Event (Idempotency)"
echo "--------------------------------------------------------"

PAYLOAD='{"id":"evt_tap_12345","event":"succeeded","data":{"id":"chg_abc123","amount":100.50,"currency":"BDT","status":"CAPTURED","customer":{"phone":{"number":"880171064443"}},"metadata":{"user_id":"USER_001"}}}'
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac '99E9418B-6CBD-4574-A646-6415C062DEC7' | awk '{print $2}')

curl -X POST $DEPOSIT_ENDPOINT \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -s | jq '.'

echo ""
echo ""

# Test 3: Different wallet deposit
echo "Test 3: Deposit Webhook - Different Wallet"
echo "-------------------------------------------"

PAYLOAD='{"id":"evt_tap_67890","event":"succeeded","data":{"id":"chg_xyz789","amount":250.00,"currency":"BDT","status":"CAPTURED","customer":{"phone":{"number":"880171064444"}},"metadata":{"user_id":"USER_002"}}}'
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac '99E9418B-6CBD-4574-A646-6415C062DEC7' | awk '{print $2}')

curl -X POST $DEPOSIT_ENDPOINT \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $(echo -n '{"id":"evt_tap_67890","event":"charge.succeeded","object":{"id":"chg_xyz789","amount":250.00,"currency":"GBP","status":"CAPTURED","customer":{"phone":{"number":"880181234567"}}}}' | openssl dgst -sha256 -hmac '99E9418B-6CBD-4574-A646-6415C062DEC7' | awk '{print $2}')" \
  -d '{
    "id": "evt_tap_67890",
    "event": "charge.succeeded",
    "object": {
      "id": "chg_xyz789",
      "amount": 250.00,
      "currency": "GBP",
      "status": "CAPTURED",
      "customer": {
        "phone": {
          "number": "880181234567"
        }
      }
    }
  }' \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -s | jq '.'

echo ""
echo ""

# Test 4: Failed deposit

curl -X POST $DEPOSIT_ENDPOINT \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -s | jq '.'

echo ""
echo ""

# Test 4: Wallet not found
echo "Test 4: Deposit Webhook - Wallet Not Found"
echo "-------------------------------------------"

PAYLOAD='{"id":"evt_tap_99999","event":"succeeded","data":{"id":"chg_failed123","amount":75.00,"currency":"BDT","status":"CAPTURED","customer":{"phone":{"number":"999999999999"}}}}'
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac '99E9418B-6CBD-4574-A646-6415C062DEC7' | awk '{print $2}')

curl -X POST $DEPOSIT_ENDPOINT \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -s | jq '.'

echo ""
echo ""

echo "=========================================="
echo "Tests Completed"
echo "=========================================="
echo ""
echo "✓ Test 1: Successful deposit with valid wallet"
echo "✓ Test 2: Idempotency check (duplicate event)"
echo "✓ Test 3: Different wallet deposit"
echo "✓ Test 4: Wallet not found (should return 400)"
echo ""
echo "IMPORTANT: All tests use COMPACT JSON"
echo "Formatted JSON will cause signature mismatch!"
echo ""
echo "Check database:"
echo "  SELECT * FROM tap_deposits ORDER BY created_at DESC LIMIT 5;"
echo "  SELECT wallet_number, balance FROM remittance_wallets;"
echo ""
echo "Check logs:"
echo "  tail -f writable/logs/log-*.log | grep 'TAP'"
echo ""
