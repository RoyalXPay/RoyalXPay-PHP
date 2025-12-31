<?php

namespace App\Controllers\API;

use App\Libraries\Thawani;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class PaymentController extends ResourceController
{
    use ResponseTrait;
    protected $thawani;

    public function __construct()
    {
        $configArray = array(
            'isTestMode' => THAWANI_ISTESTMODE,
            'public_key' => THAWANI_PUBLICKEY,
            'private_key' => THAWANI_PRIVATEKEY,
        );
        $this->thawani = new Thawani($configArray);
    }

    public function getPaymentUrl()
    {
        $userid = $this->request->getVar('user_id');
        $packageid = $this->request->getVar('package_id');

        if ($userid && $packageid) {
            $packageamount = 100;
            $amountInBaisa = $packageamount * 1000;

            // Prepare the payment data
            $input = [
                'client_reference_id' => $packageid,
                'products' => [
                    ['name' => 'Package Name', 'unit_amount' => (int) $amountInBaisa, 'quantity' => 1]
                ],
                'success_url' => site_url('payment/success'),
                'cancel_url' => site_url('payment/cancel'),
                'metadata' => [
                    'order_id' => $packageid,
                    'customer_id' => $userid
                ]
            ];

            // Call payment gateway API to generate payment URL
            $paymenturl = $this->thawani->generatePaymentUrl($input);
            $responseData = $this->thawani->responseData;

            if (isset($responseData['success']) && $responseData['success'] == 1) {
                $sessionid = $responseData['data']['session_id'];
                // Save session id to the database if necessary

                return $this->respondCreated([
                    'status' => 201,
                    'paymenturl' => $paymenturl,
                    'message' => 'Payment URL generated successfully'
                ]);
            } else {
                return $this->failNotFound('Error generating payment URL.');
            }
        } else {
            return $this->failNotFound('Missing userid or packageid.');
        }
    }

    public function success()
    {
        return $this->respond([
            'status' => 'success',
            'message' => 'Payment successful!',
        ]);
    }

    public function cancel()
    {
        return $this->respond([
            'status' => 'cancel',
            'message' => 'Payment was canceled.',
        ]);
    }
}
