<?php
// Disable debug output
if (function_exists('ini_set')) {
    ini_set('display_errors', 0);
}
error_reporting(0);

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Backend URL
$apiUrl = "https://interactivebyskiphi.skiphi.com/core/generate-token/";

// Read raw input
$body = file_get_contents("php://input");

// Init curl
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    http_response_code(502);
    echo json_encode(["error" => "cURL error: " . $err]);
    exit;
}

// Forward backend response
http_response_code($httpCode);
echo $response;
exit;
