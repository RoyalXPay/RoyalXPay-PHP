<?php 

// app/Controllers/Frontend.php

namespace App\Controllers;

class Frontend extends BaseController
{

    public function index()
    {
        return view('frontend/index'); // Loads app/Views/frontend/search-page.php
    }

    public function searchPage()
    {
        return view('frontend/search-page'); // Loads app/Views/frontend/search-page.php
    }

    public function menupage()
    {
        return view('frontend/menu-page'); // Loads app/Views/frontend/search-page.php
    }

  public function call()
    {
        return view('frontend/call'); // Loads app/Views/frontend/search-page.php
    }
     public function chat()
    {
        return view('frontend/chat'); // Loads app/Views/frontend/search-page.php
    }
     public function videocall()
    {
        return view('frontend/video-call'); // Loads app/Views/frontend/search-page.php
    }

public function termsConditions()
{
    return view('frontend/terms_conditions');
}
      // Token Proxy Endpoint
    public function proxy()
    {
        $clientCode = "CLTTEST001"; // replace with your actual client_code

        $ch = curl_init("https://interactivebyskiphi.skiphi.com/core/generate-token/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "client_code" => $clientCode
        ]));

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return $this->response->setJSON([
                "success" => false,
                "error"   => $error
            ]);
        }

        return $this->response->setJSON(json_decode($response, true));
    }

    public function recharge()
    {
        return view('frontend/recharge'); // Loads app/Views/frontend/search-page.php
    }

    public function contactus()
    {
        return view('frontend/contact-us'); // Loads app/Views/frontend/search-page.php
    }

    public function discountloyalities()
    {
        return view('frontend/discount-loyalities'); // Loads app/Views/frontend/search-page.php
    }
     public function company()
    {
        return view('frontend/company'); // Loads app/Views/frontend/search-page.php
    }
     public function paymentservices()
    {
        return view('frontend/payment-services'); // Loads app/Views/frontend/search-page.php
    }
     public function rechargebill()
    {
        return view('frontend/recharge-bill'); // Loads app/Views/frontend/search-page.php
    }
     public function wealth()
    {
        return view('frontend/wealth'); // Loads app/Views/frontend/search-page.php
    }
}


?>