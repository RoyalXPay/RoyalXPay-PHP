<?php
global $title;
global $metaTags;

use App\Models\CartModel;

/** function for include title of the page
 *
 */
if (!function_exists('include_title')) {
    function include_title()
    {
        global $title;
        echo "<title>" . "EMS" . "</title>";
    }
}

/**
 *  function foer set page title at run time
 */
if (!function_exists('set_title')) {
    function set_title($titlevalue)
    {
        global $title;
        $title = $titlevalue;
    }
}

/**
 * function get meta tag into header
 */
if (!function_exists('include_metas')) {
    function include_metas()
    {
        global $metaTags;
        if (is_array($metaTags)) {
            foreach ($metaTags as $key => $value) {
                echo "<meta name='" . $key . "' content='" . $value . "'>";
            }
        }
    }
}

/**
 * function for set meta tag for SEO at any place
 */
if (!function_exists('set_metas')) {
    function set_metas($arrmeta)
    {
        global $metaTags;

        foreach ($arrmeta as $key => $value) {
            $metaTags[$key] = $value;
        }
    }
}

if (!function_exists('sanitize_string')) {
    function sanitize_string($var = '')
    {
        if (empty($var)) {
            return $var;
        }

        if (is_array($var)) {
            foreach ($var as $key => $value) {
                $var[$key] = sanitize_string($value);
            }
            return $var;
        }

        $var = trim($var);
        $var = stripslashes($var);
        $var = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        return $var;
    }
}

if (!function_exists('array_to_object')) {
    function array_to_object($array)
    {
        if (!is_array($array) && !is_object($array)) {
            return new \stdClass();
        }
        return json_decode(json_encode((object)$array));
    }
}

if (!function_exists('formated_time')) {
    function formated_time($targetDate)
    {
        $dateFormat = date('d-m-Y H:i:s');

        if (empty($targetDate)) {
            return "Unknown";
        }

        // Current time as MySQL datetime value
        $currentDateTime = date('Y-m-d H:i:s');
        // Target time as Unix timestamp
        $targetTimestamp = strtotime($targetDate);
        $currentTimestamp = strtotime($currentDateTime);

        // Calculate the time difference in minutes
        $timeDifferenceInMinutes = floor(abs($currentTimestamp - $targetTimestamp) / 60);

        // Determine whether the time difference needs to be in minutes, hours, or days
        if ($timeDifferenceInMinutes < 2) {
            return "Just now";
        } elseif ($timeDifferenceInMinutes < 60) {
            return $timeDifferenceInMinutes . " minutes ago";
        } elseif ($timeDifferenceInMinutes < 120) {
            return floor(abs($timeDifferenceInMinutes / 60)) . " hour ago";
        } elseif ($timeDifferenceInMinutes < 1440) {
            return floor(abs($timeDifferenceInMinutes / 60)) . " hours ago";
        } elseif ($timeDifferenceInMinutes < 2880) {
            return floor(abs($timeDifferenceInMinutes / 1440)) . " day ago";
        } else {
            return date($dateFormat, $targetTimestamp);
        }
    }
}

if (!function_exists('include_css_cdn')) {
    function include_css_cdn()
    {
        global $css_cdn_urls;
        if (isset($css_cdn_urls) && is_array($css_cdn_urls)) {
            foreach ($css_cdn_urls as $url) {
                echo '<link href="' . base_url($url) . '" rel="stylesheet" type="text/css">' . "\n";
            }
        }
    }
}

if (!function_exists('add_css_cdn')) {

    function add_css_cdn(...$urls)
    {
        global $css_cdn_urls;
        foreach ($urls as $url) {
            $css_cdn_urls[] = $url;
        }
    }
}

if (!function_exists('include_js_cdn')) {
    function include_js_cdn()
    {
        global $js_cdn_urls;
        if (isset($js_cdn_urls) && is_array($js_cdn_urls)) {
            foreach ($js_cdn_urls as $url) {
                echo '<script src="' . base_url($url) . '"></script>' . "\n";
            }
        }
    }
}

if (!function_exists('add_js_cdn')) {

    function add_js_cdn(...$urls)
    {
        global $js_cdn_urls;
        foreach ($urls as $url) {
            $js_cdn_urls[] = $url;
        }
    }
}

if (!function_exists('printArr')) {

    /**
     * Prints an array or object in a readable format.
     *
     * @param mixed $data The data to be printed.
     * @return void
     */
    function printArr($data)
    {
        // Open a <pre> tag for formatted output
        echo "<pre>";

        // Print the data in a readable format
        print_r($data);

        // Close the <pre> tag
        echo "</pre>";
    }
}


if (!function_exists('getLocalityFromLatLng')) {
    function getLocalityFromLatLng($lat, $lng)
    {
        if (empty($lat) || empty($lng)) {
            return 'Unknown';
        }

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}";

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: MyApp/1.0 (your-email@example.com)\r\n"
            ]
        ];

        $context = stream_context_create($opts);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return 'Unknown location';
        }

        $data = json_decode($response, true);
        return $data['display_name'] ?? 'Unknown location';
    }
}


if (!function_exists('formatWords')) {
    /**
     * Capitalize the first letter of each word in a string.
     * If an underscore is found, replace it with a space and capitalize words.
     * If no underscore is found, convert the whole string to uppercase.
     *
     * @param string $str The string to format
     * @return string The formatted string
     */
    function formatWords($str)
    {
        // Check if an underscore exists in the string
        if (strpos($str, '_') !== false) {
            // Replace underscores with spaces and capitalize each word
            return ucwords(str_replace('_', ' ', strtolower($str)));
        } else {
            // If no underscore, just capitalize the entire string
            return ucwords($str);
        }
    }
}

if (!function_exists('getCartData')) {
    /**
     * Get cart items, count, and total for the current user
     * 
     * @return array
     */
    function getCartData()
    {
        $cartModel = new CartModel();
        $data = [
            'cartItems' => [],
            'cartCount' => 0,
            'cartTotal' => 0
        ];

        if (session()->has('user_id')) {
            $userId = session()->get('user_id');
            $data['cartItems'] = $cartModel->getUserCart($userId);
            $data['cartCount'] = $cartModel->getCartCount($userId);
            $data['cartTotal'] = $cartModel->getCartTotal($userId);
        }

        return $data;
    }
}

if (!function_exists('get_subscription_durations')) {
    function get_subscription_durations()
    {
        return [
            1 => '1 Month',
            2 => '2 Months',
            3 => '3 Months',
            4 => '4 Months',
            5 => '5 Months',
            6 => '6 Months',
            7 => '7 Months',
            8 => '8 Months',
            9 => '9 Months',
            10 => '10 Months',
            11 => '11 Months',
            12 => '12 Months'
        ];
    }
}

