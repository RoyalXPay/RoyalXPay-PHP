#!/bin/bash

# TAP Webhook Test Script (New TapWebhookFilter - HMAC Only)
# This tests the webhook with the new dedicated filter

WEBHOOK_URL="http://localhost:8080/api/v1/tap/deposit-webhook"
WEBHOOK_SECRET="99E9418B-6CBD-4574-A646-6415C062DEC7"

# Test payload - COMPACT JSON
PAYLOAD='{"id":"evt_TAP_FILTER_TEST_001","event":"succeeded","data":{"id":"chg_TAP_FILTER_TEST_001","amount":50.00,"currency":"BDT","status":"CAPTURED","customer":{"phone":{"number":"880171064443"}},"metadata":{"user_id":"1"}}}'

# Generate HMAC signature
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac "$WEBHOOK_SECRET" | awk '{print $2}')

echo "========================================="
echo "TAP Webhook Test - New TapWebhookFilter"
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

# Send request (NO API KEY - only HMAC signature)
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Tap-Signature: $SIGNATURE" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -v

echo ""
echo "========================================="
echo "Test completed!"
echo "========================================="
