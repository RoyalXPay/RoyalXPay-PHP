<?php

namespace App\Libraries;

class Thawani
{
    const production = 'https://checkout.thawani.om/api/v1/';
    const uat = 'https://uatcheckout.thawani.om/api/v1/';

    public $debug = false;
    public $payment_id = '';
    public $payment_status = 0;
    public $connectTimeOut = 30;

    public $intentId;
    public $connection;
    public $headerCookies;

    public $errorFatal;
    public $errorWarning;
    public $headerResponses;

    public $config = [];
    public $responseData = [];

    /**
     * Thawani constructor.
     * @param array $config Configuration parameters
     * @param string|callable $errorFatal Optional fatal error handler callback
     */
    public function __construct(array $config, $errorFatal = '')
    {
        $this->config = array_merge([
            'isTestMode' => 1, // 1 for test mode
            'public_key' => '',
            'private_key' => '',
            'webhookSecret' => ''
        ], $config);

        $this->errorFatal = $errorFatal;

        foreach ($this->config as $key => $value) {
            if (in_array($key, ['isTestMode', 'remoteLicenseOnly', 'webhookSecret'])) continue;

            if (empty($value)) {
                if (is_callable($this->errorFatal)) {
                    call_user_func_array($this->errorFatal, ['Thawani ' . $key . ' is not set']);
                } else {
                    trigger_error('Thawani ' . $key . ' is not set', E_USER_ERROR);
                }
            }
        }
    }

    /**
     * Check if the current connection is using SSL.
     * @return bool True if SSL is used, false otherwise.
     */
    public function isSsl(): bool
    {
        return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off';
    }

    /**
     * Get the current page URL.
     * @return string The current URL
     */
    public function currentPageUrl(): string
    {
        return urldecode(($this->isSsl() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * Get the current page URL without query parameters.
     * @return string The current page URL without query parameters
     */
    public function currentPagePlain(): string
    {
        return substr($this->currentPageUrl(), 0, strpos($this->currentPageUrl(), '?'));
    }

    /**
     * Get the full URL based on the environment (Test/Production).
     * @param string $parameter The URL parameter.
     * @return string The full URL.
     */
    public function getUrl(string $parameter): string
    {
        return ($this->config['isTestMode'] == 1 ? self::uat : self::production) . $parameter;
    }

    /**
     * Send a POST request using cURL.
     * @param array $parameters The parameters for the request.
     * @param string $method The HTTP method (POST/GET).
     * @return array The response from the server.
     */
    public function post(array $parameters, string $method = 'POST'): array
    {
        $output = ['is_success' => 0, 'response' => ''];
        $parameters = array_merge([
            'url' => '',
            'settings' => [],
            'fields' => [],
            'headers' => [],
            'printHeader' => false,
            'skipSSL' => false,
        ], $parameters);

        if (!is_array($parameters['fields'])) {
            $parameters['fields'] = [];
        }

        $fieldsString = json_encode($parameters['fields'], JSON_UNESCAPED_UNICODE);
        $parameters['headers'] = array_merge(['Content-Type' => 'application/json'], $parameters['headers']);
        $headers = array_map(fn($v, $k) => "$k: $v", $parameters['headers'], array_keys($parameters['headers']));

        $settings = [
            CURLOPT_URL => $parameters['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->connectTimeOut,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeOut,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method == 'GET' && count($parameters['fields']) > 0) {
            $settings[CURLOPT_URL] .= (strpos($parameters['url'], '?') !== false ? '&' : '?') . http_build_query($parameters['fields']);
        } else {
            $settings[CURLOPT_POSTFIELDS] = $fieldsString;
        }

        foreach ($parameters['settings'] as $key => $value) {
            $settings[$key] = $value;
        }

        $settings[CURLOPT_HEADER] = $parameters['printHeader'] ? 1 : 0;
        $settings[CURLOPT_SSL_VERIFYPEER] = $parameters['skipSSL'] ? 1 : 0;

        $this->connection = curl_init();
        curl_setopt_array($this->connection, $settings);

        $content = curl_exec($this->connection);
        $err = curl_error($this->connection);

        if ($parameters['printHeader']) {
            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $content, $matches);
            $this->headerCookies = array();
            foreach ($matches[1] as $item) {
                parse_str($item, $cookie);
                $this->headerCookies = array_merge($this->headerCookies, $cookie);
            }
            $headerLen = curl_getinfo($this->connection, CURLINFO_HEADER_SIZE);
            $this->headerResponses = substr($content, 0, $headerLen);
            $content = substr($content, $headerLen);
        }

        curl_close($this->connection);

        if (!$err) {
            $output['is_success'] = 1;
            $output['response'] = $content;
        } else {
            $output['response'] = $err;
        }

        return $output;
    }

    /**
     * Process user meta data and sanitize product names.
     * @param array $input The input data
     * @return array The processed data
     */
    public function processUserMeta(array $input): array
    {
        if (isset($input['products']) && is_array($input['products'])) {
            foreach ($input['products'] as $i => $product) {
                $input['products'][$i]['name'] = trim($product['name']);
                $input['products'][$i]['name'] = strlen($product['name']) > 40 ? mb_substr($product['name'], 0, 37, 'utf-8') . '...' : $product['name'];
            }
        }
        return $input;
    }

    /**
     * Generate a payment URL based on the user input.
     * @param array $input The input data
     * @return string The payment URL
     */
    public function generatePaymentUrl(array $input): string
    {
        $output = '';
        $input = $this->processUserMeta($input);

        $server = $this->post([
            'url' => $this->getUrl('checkout/session'),
            'fields' => $input,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key'],
                'Content-Type' => 'application/json',
            ]
        ], 'POST');

        if (isset($server['response']) && !empty($server['response'])) {
            $server['response'] = json_decode($server['response'], true);
            $this->responseData = $server['response'];

            if (isset($server['response']['success']) && $server['response']['success'] == 1 && !empty($server['response']['data']['session_id'])) {
                $this->payment_id = $server['response']['data']['session_id'];
                $output = $this->config['isTestMode'] == 1
                    ? 'https://uatcheckout.thawani.om/pay/' . $this->payment_id . '?key=' . $this->config['public_key']
                    : 'https://checkout.thawani.om/pay/' . $this->payment_id . '?key=' . $this->config['public_key'];
            }
        }

        return $output;
    }

    /**
     * Check the payment status for a given session ID.
     * @param string $session_id The session ID
     * @return array The payment status
     */
    public function checkPaymentStatus(string $session_id): array
    {
        $output = [];
        $server = $this->post([
            'url' => $this->getUrl('checkout/session') . '/' . $session_id,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], 'GET');

        if (isset($server['response']) && !empty($server['response'])) {
            $server['response'] = json_decode($server['response'], true);
            $server['response']['data'] = isset($server['response']['data'][0]) ? $server['response']['data'][0] : $server['response']['data'] ?? [];

            if (isset($server['response']['success']) && $server['response']['success'] === 1 && !empty($server['response']['data'])) {
                if (strtolower($server['response']['data']['payment_status']) === 'paid') {
                    $this->payment_status = 1;
                }
                $output = $server['response']['data'];
            }
        }

        return $output;
    }

    /**
     * Handle customer operations.
     * @param string $customer_id The customer ID
     * @param string $method The operation method ('create', 'get', 'delete')
     * @return array|mixed The result of the operation
     */
    private function customer(string $customer_id, string $method = 'create')
    {
        $output = [];
        $methods = [
            'create' => 'POST',
            'get' => 'GET',
            'delete' => 'DELETE',
        ];

        $server = $this->post([
            'url' => $this->getUrl('customers') . ($method === 'delete' ? '/' . $customer_id : ''),
            'fields' => ['client_customer_id' => $customer_id],
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], $methods[$method]);

        if (isset($server['response']) && !empty($server['response'])) {
            $server['response'] = json_decode($server['response'], true);
            if ($method !== 'delete') {
                $server['response']['data'] = isset($server['response']['data'][0]) ? $server['response']['data'][0] : $server['response']['data'] ?? [];
            }

            if (isset($server['response']['success']) && $server['response']['success'] === 1 && !empty($server['response']['data'])) {
                $output = $server['response']['data'];
            }
        }

        return $output;
    }

    /**
     * Create a customer.
     * @param string $customer_id The customer ID
     * @return array|mixed The result
     */
    public function createCustomer(string $customer_id)
    {
        return $this->customer($customer_id, 'create');
    }

    /**
     * Get a customer's details.
     * @param string $customer_id The customer ID
     * @return array|mixed The customer details
     */
    public function getCustomer(string $customer_id)
    {
        return $this->customer($customer_id, 'get');
    }

    /**
     * Delete a customer.
     * @param string $customer_id The customer ID
     * @return array|mixed The result
     */
    public function deleteCustomer(string $customer_id)
    {
        return $this->customer($customer_id, 'delete');
    }

    /**
     * Get a customer's payment methods.
     * @param string $customer_id The customer ID
     * @return array|mixed The payment methods
     */
    public function getPaymentMethods(string $customer_id)
    {
        $server = $this->post([
            'url' => $this->getUrl('payment_methods'),
            'fields' => ['customerId' => $customer_id],
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ],
        ], 'GET');

        return $server['is_success'] == 1 ? json_decode($server['response'], true) : [];
    }

    /**
     * @param $token
     * @return array|mixed
     */
    public function deletePaymentMethods($token)
    {
        $server = $this->post([
            'url' => $this->getUrl('payment_methods') . '/' . $token,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ],
        ], 'DELETE');

        if ($server['is_success'] == 1) {
            return !empty($server['response']) ? json_decode($server['response'], true) : [];
        }
        return $server['response'];
    }

    /**
     * @param string $payment_id
     * @param bool $ico Set to true if you want to use checkout_invoice instead of payment_id
     * @return array|mixed
     */
    public function getPaymentDetails($payment_id = '', $ico = true)
    {
        $url = $this->getUrl('payments' . (!empty($payment_id) ? ($ico ? '?checkout_invoice=' : '/') : '') . $payment_id);
        $server = $this->post([
            'url' => $url,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], 'GET');

        if ($server['is_success'] == 1) {
            return !empty($server['response']) ? json_decode($server['response'], true) : [];
        }

        return $server['response'];
    }

    /**
     * @param $payment_id
     * @param string $reason
     * @param array $metadata
     * @return array
     */
    public function refundPayment($payment_id, $reason = '', $metadata = [])
    {
        $input = [
            'payment_id' => $payment_id,
            'reason' => !empty($reason) ? $reason : 'Unspecified',
            'metadata' => $metadata
        ];

        $server = $this->post([
            'url' => $this->getUrl('refunds'),
            'fields' => $input,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], 'POST');

        if ($server['is_success'] == 1) {
            return !empty($server['response']) ? json_decode($server['response'], true) : [];
        }

        return $server['response'];
    }

    /**
     * @param array $input
     * @return array|mixed
     */
    public function createIntent(array $input)
    {
        $input = array_merge([
            'client_reference_id' => '',
            'return_url' => '',
            'metadata' => '',
            'payment_method_id' => '',
            'amount' => ''
        ], $input);

        $server = $this->post([
            'url' => $this->getUrl('payment_intents'),
            'fields' => $input,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], 'POST');

        if ($server['is_success'] == 1) {
            $response = !empty($server['response']) ? json_decode($server['response'], true) : [];
            if ($response['code'] == 2001 && isset($response['data']['id'])) {
                $this->intentId = $response['data']['id'];
                $response['confirm'] = $this->getUrl('payment_intents') . $response['data']['id'] . '/confirm';
            }
            return $response;
        }

        return $server['response'];
    }

    /**
     * @param $intentId
     * @return array|mixed
     */
    public function chargeCard($intentId = '')
    {
        $intentId = empty($intentId) ? $this->intentId : $intentId;
        $server = $this->post([
            'url' => $this->getUrl('payment_intents') . '/' . $intentId . '/confirm',
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], 'POST');

        if ($server['is_success'] == 1) {
            return !empty($server['response']) ? json_decode($server['response'], true) : [];
        }

        return $server['response'];
    }

    /**
     * @param $intentId
     * @return array|mixed
     */
    public function getIntent($intentId = '')
    {
        $intentId = empty($intentId) ? $this->intentId : $intentId;
        $server = $this->post([
            'url' => $this->getUrl('payment_intents') . '/' . $intentId,
            'headers' => [
                'Thawani-Api-Key' => $this->config['private_key']
            ]
        ], 'GET');

        if ($server['is_success'] == 1) {
            $response = !empty($server['response']) ? json_decode($server['response'], true) : [];
            if (isset($response['data']['status']) && strtolower($response['data']['status']) == 'succeeded') {
                $this->payment_status = 1;
            }
            return $response;
        }

        return $server['response'];
    }

    /**
     * @param int $capture Set to 1 to save the POST data in a file in /logs/{PAYMENT_ID}.json
     * @return array
     */
    public function handleCallback($capture = 0)
    {
        $input = [
            'body' => file_get_contents("php://input"),
            'headers' => getallheaders()
        ];

        $output = [
            'is_success' => 0,
            'receipt' => 0,
            'session' => [],
            'raw' => []
        ];

        $input['body'] = json_decode($input['body'], true) ?: [];
        $output['raw'] = $input['body']['data'] ?? [];
        $output['raw']['event_type'] = $input['body']['event_type'] ?? '';
        $output['headers'] = array_change_key_case($input['headers'], CASE_LOWER);

        if (strtolower($output['raw']['event_type']) == 'checkout.completed' && strtolower($output['raw']['payment_status'] ?? '') == 'paid') {
            $signature = $output['headers']['thawani-signature'] ?? '';
            $timestamp = $output['headers']['thawani-timestamp'] ?? '';

            if (isset($this->config['webhookSecret']) && !empty($this->config['webhookSecret']) && $signature == hash_hmac('sha256', json_encode($input['body']) . "-" . $timestamp, $this->config['webhookSecret'])) {
                $output['is_success'] = 1;
                $output['receipt'] = (int)($output['raw']['invoice'] ?? 0);
                $this->payment_status = 1;
            } elseif (isset($output['raw']['session_id'])) {
                $output['session'] = $this->checkPaymentStatus($output['raw']['session_id']);
                $output['is_success'] = $this->payment_status;
                $output['receipt'] = (int)($output['raw']['invoice'] ?? 0);
            }
        }

        if ($capture == 1) {
            $this->captureCallback($input);
        }

        return $output;
    }

    /**
     * @param $input
     */
    public function captureCallback(array $input)
    {
        $basePath = __DIR__;
        $filePath = $basePath . '/logs';
        $fileName = $input['body']['receipt'] ?? time();

        if (!is_dir($filePath)) {
            if (is_writable($basePath)) {
                mkdir($filePath, 0777);
                $this->protect_directory_access($filePath, 'comprehensive');
            } else {
                trigger_error('Path <b>' . $filePath . '</b> is not writable', E_USER_ERROR);
            }
        }

        $fileName = $filePath . '/' . $fileName . '.json';
        file_put_contents($fileName, json_encode($input));
    }

    /**
     * @param $directory
     * @param string $type
     */
    public function protect_directory_access($directory, $type = 'access')
    {
        $file = $directory . '/.htaccess';
        if (!is_dir($directory)) {
            $directory = '../' . $directory;
        }

        if (!file_exists($file)) {
            $file = is_dir($directory) ? $directory . '/.htaccess' : '../' . $directory . '/.htaccess';
            file_put_contents($file, ($type == 'comprehensive' ? 'deny from all' : 'Options -Indexes'));
        }
    }

    /**
     * @param $input
     * @param int $return
     * @param string $cssClass
     * @return string
     */
    public function iprint_r($input, $return = 0, $cssClass = '')
    {
        $output = '<pre dir="ltr"' . (!empty($cssClass) ? ' class="' . $cssClass . '"' : '') . '>' . (is_array($input) || is_object($input) ? print_r($input, true) : $input) . '</pre>';

        if (isset($this->errorWarning) && is_callable($this->errorWarning)) {
            call_user_func_array($this->errorWarning, [$output]);
        } else {
            if ($return != 0) return $output;
            else echo $output;
        }
    }

    /**
     * @param $input
     * @return array
     */
    public function addonParser($input)
    {
        $output = [];
        $list = explode('|', $input);

        foreach ($list as $item) {
            $item = explode(';', $item);
            if (isset($item[2]) && strpos($item[2], 'status=') !== false) {
                $key = '';
                foreach ($item as $param) {
                    $param = explode('=', $param);
                    if (isset($param[0], $param[1])) {
                        if ($param[0] == 'name') {
                            $key = stripos($param[1], 'Credit') !== false ? 'credit_cards' : $param[1];
                        }
                        if ($key && $param[0] == 'nextduedate' && ($param[1] == '0000-00-00' || new \DateTime() < new \DateTime($param[1] . ' 23:59:59'))) {
                            $output[$key][$param[0]] = $param[1];
                            $output[$key]['status'] = substr($item[2], strlen('status='));
                        }
                    }
                }
            }
        }

        return $output;
    }
}
