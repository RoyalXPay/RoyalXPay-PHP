#!/bin/bash

# TAP Webhook Test - Data Field Only
WEBHOOK_URL="http://localhost:8080/api/v1/tap/deposit-webhook"
WEBHOOK_SECRET="99E9418B-6CBD-4574-A646-6415C062DEC7"

# Test payload with 'data' field - COMPACT JSON
PAYLOAD='{"id":"evt_TAP_TEST_002","event":"succeeded","data":{"id":"chg_TAP_TEST_002","amount":100.00,"currency":"BDT","status":"CAPTURED","customer":{"phone":{"number":"880171064443"}}}}'

# Generate HMAC signature
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac "$WEBHOOK_SECRET" | awk '{print $2}')

echo "========================================="
echo "TAP Webhook Test - Using 'data' field"
echo "========================================="
echo ""
echo "Endpoint: $WEBHOOK_URL"
echo "Signature: $SIGNATURE"
echo ""
echo "Payload (COMPACT JSON):"
echo "$PAYLOAD"
echo ""
echo "Sending request..."
echo ""

# Send request
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n"

echo ""
echo "========================================="
