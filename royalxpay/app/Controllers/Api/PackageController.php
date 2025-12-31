<?php

namespace App\Controllers\API;

use App\Libraries\Thawani;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class PackageController extends ResourceController
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
        // Validate the input
        if (!$this->validate([
            'supplier_id'   => 'required',
            'amount'        => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'permit_empty|numeric',
            'customer_email' => 'permit_empty|valid_email',
        ])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => false,
                    'error' => 'Validation failed.',
                    'messages' => $this->validator->getErrors(),
                ]);
        }

        $amount = $this->request->getVar('amount');
        $supplierid = $this->request->getVar('supplier_id');
        $customerName = $this->request->getVar('customer_name');
        $customerEmail = $this->request->getVar('customer_email') ?? "";
        $customerPhone = $this->request->getVar('customer_phone') ?? "";

        // Convert price to the appropriate currency unit (e.g., baisa or cents)
        $amountInBaisa = (float) $amount * 1000;

        // Prepare the payment data
        $input = [
            'client_reference_id' => $supplierid,
            'products' => [
                ['name' => $customerName, 'unit_amount' => (int) $amountInBaisa, 'quantity' => 1]
            ],
            'success_url' => site_url('api/payment/success'),
            'cancel_url' => site_url('api/payment/cancel'),
            'metadata' => [
                'order_id' => $supplierid,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone
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
    }

    public function success()
    {
        // You can set a flash message for success
        session()->setFlashdata('message', 'Payment successful!');

        // Return the success view
        return view('paymentGateway/success');
    }

    public function cancel()
    {
        // You can set a flash message for cancellation
        session()->setFlashdata('message', 'Payment was canceled.');

        // Return the failure view
        return view('paymentGateway/failure');
    }
}
