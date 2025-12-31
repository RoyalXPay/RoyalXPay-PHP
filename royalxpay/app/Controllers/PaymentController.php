<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Models\ContractModel;
use App\Models\ContractPaymentsModel;
use App\Controllers\BaseController;

class PaymentController extends BaseController
{
    public function index()
    {
        set_title('Rent Contracts | ' . SITE_NAME);

        // Check for contract_id in the GET request
        $contractId = $this->request->getGet('contract_id');
        $data = [
            'action' => "contract-payment-history?contract_id=" . $contractId,
            'pageTitle' => "Rent Contracts Payment List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Initialize models and pagination
        $customPagination = new Pagination();
        $contractModel = new ContractModel();
        $paymentsModel = new ContractPaymentsModel();

        // Collect search criteria
        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 20;

        // Get the total record count based on search criteria (if any)
        $totalRecord = $paymentsModel->getContractPayments($contractId, $data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch results for the current page based on search criteria
        $data['results'] = $paymentsModel->getContractPayments($contractId, $data['searchArray'], $startLimit, $Limit);
        $data['lastPaymentHistory'] = reset($data['results']);
        $data['contractDetails'] = $contractModel->find($contractId);
        // Return the view with the data
        return view('admin/payments/index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('payment_id');
        $validation = \Config\Services::validation();

        // Validate the form data
        if (!$this->validate([
            'contract_id'     => 'required|numeric',
            'payment_date'     => 'required|valid_date',
            'payment_amount'   => 'required|numeric',
            'final_amount'     => 'required|numeric',
            'balance_amount'   => 'required|numeric',
            'payment_method'   => 'required|string'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $paymentAmount = (float) $this->request->getPost('payment_amount');
        $finalAmount = (float) $this->request->getPost('final_amount');

        // Calculate balance amount
        $balanceAmount = $finalAmount - $paymentAmount;

        // Determine payment status
        $paymentStatus = $this->request->getPost('payment_status');
        if (empty($paymentStatus)) {
            $paymentStatus = ($balanceAmount > 0) ? 'Partially Paid' : 'Completed';
        }

        // Initialize the model
        $paymentHistoryModel = new ContractPaymentsModel();

        // Prepare the base data
        $data = [
            'contract_id'     => $this->request->getPost('contract_id'),
            'payment_date'     => $this->request->getPost('payment_date'),
            'payment_amount'   => $this->request->getPost('payment_amount'),
            'final_amount'     => $this->request->getPost('balance_amount'),
            'balance_amount'   => $this->request->getPost('balance_amount'),
            'payment_status'   => $paymentStatus,
            'payment_method'   => $this->request->getPost('payment_method'),
            'notes'            => $this->request->getPost('notes') ?? '',
        ];

        // Include additional fields based on payment method
        $paymentMethod = $this->request->getPost('payment_method');

        if ($paymentMethod === 'Cheque') {
            $data['cheque_date']    = $this->request->getPost('cheque_date') ?? null;
            $data['cheque_number']  = $this->request->getPost('cheque_number') ?? null;
            $data['bank_name']      = $this->request->getPost('bank_name') ?? null;
        }

        if ($paymentMethod === 'Online') {
            $data['transaction_id'] = $this->request->getPost('transaction_id') ?? null;
        }

        // File upload handling
        $uploadPath = FCPATH . 'uploads/payment_receipts';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $receiptFile = $this->request->getFile('receipt');
        if ($receiptFile && $receiptFile->isValid() && !$receiptFile->hasMoved()) {
            $receiptFileName = $receiptFile->getRandomName();
            if ($receiptFile->move($uploadPath, $receiptFileName)) {
                $data['receipt'] = $receiptFileName;
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Failed to upload receipt.',
                ]);
            }
        }

        // Save or update the payment history
        if ($id) {
            if ($paymentHistoryModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Payment history updated successfully',
                ]);
            }
        } else {
            if ($paymentHistoryModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Payment history added successfully',
                ]);
            }
        }

        // If saving/updating failed
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save payment history. Please try again.',
        ]);
    }

    public function edit($id)
    {
        $paymentModel = new ContractPaymentsModel();
        $paymentDetails = $paymentModel->find($id);

        if ($paymentDetails) {
            return $this->response->setJSON($paymentDetails);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'payment not found']);
        }
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Initialize the models
                $paymentHistoryModel = new ContractPaymentsModel();

                // Check if the payment history exists for the given ID
                $paymentHistory = $paymentHistoryModel->find($id);

                // If no payment history record is found, return an error response
                if (!$paymentHistory) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Payment history not found.',
                    ]);
                }

                // If the contract is found, adjust the final payable amount
                if ($paymentHistory) {
                    // Deduct the payment amount from the final payable amount
                    $newFinalAmount = $paymentHistory['final_amount'] + $paymentHistory['payment_amount'];
                    $newBalanceAmount = $paymentHistory['balance_amount'] + $paymentHistory['payment_amount'];

                    // Update the contract's final payable amount
                    $paymentHistoryModel->update($id, [
                        'final_amount' => $newFinalAmount,
                        'balance_amount' => $newBalanceAmount
                    ]);
                }

                // Proceed with deletion of the payment history record
                $paymentHistoryModel->delete($id);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment history deleted successfully',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. Please try again.',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid ID provided.',
            ]);
        }
    }

    public function generateInvoice($contractId)
    {
        $contractModel = new ContractModel();
        $paymentsModel = new ContractPaymentsModel();

        $contractDetails = $contractModel->getContractWithDetails($contractId);
        $contractPaymentDetails = $paymentsModel->getContractPayments($contractId);

        // Base URL for the images
        $headerImagePath = base_url('assets/images/invoice_header.jpg');
        $firstPageFooter = base_url('assets/images/footer_first.jpg');

        // Load mPDF Library with custom margins
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 25,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 3
        ]);

        // Header
        $mpdf->SetHTMLHeader('
            <div style="position: absolute; top: 0; left: 0; width: 100%;">
                <img src="' . $headerImagePath . '" alt="Contract Header" width="100%">
            </div>
        ', 'O');

        // Footer
        $mpdf->SetHTMLFooter('
            <div style="position: absolute; bottom: 5px; left: 0; height: 30px; width: 100%; text-align: center;">
                <img src="' . $firstPageFooter . '" style="width:100%;" />
            </div>
        ', 'O');

        $totalPaid = 0;
        foreach ($contractPaymentDetails as $paymentDetail) {
            $totalPaid += floatval($paymentDetail->payment_amount);
        }

        $totalAmount = floatval($contractDetails['final_payable_amount']);
        $balanceAmount = $totalAmount - $totalPaid;

        $html = '
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        height: 100%;
                        position: relative;
                    }
                </style>
            </head>
            <body>
                <div style="background-color: #8a2be2; height: 10px; padding: 5px; margin-top: 5px;"></div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 70%; padding: 10px; font-weight: bold; background-color: #e8d7eb;">
                            CLIENT NAME
                            <span style="font-weight: normal;">' . $contractDetails["customer_name"] . '</span>
                        </td>
                        <td style="width: 5px;"></td>
                        <td style="width: 30%; padding: 10px; font-weight: bold; background-color: #f5ebf8;">
                            CONTRACT NO
                            <span style="font-weight: normal; color: #512b58;">' . $contractDetails["contract_code"] . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70%; padding: 10px; font-weight: bold; background-color: #e8d7eb;">
                            CLIENT NUMBER
                            <span style="font-weight: normal; color: #512b58;">' . $contractDetails["phone"] . '</span>
                        </td>
                        <td style="width: 5px;"></td>
                        <td style="width: 30%; padding: 10px; font-weight: bold; background-color: #f5ebf8;">
                            CONTRACT DATE
                            <span style="font-weight: normal; color: #512b58;">' . date('d/m/Y', strtotime($contractDetails["contract_date"])) . '</span>
                        </td>
                    </tr>
                </table>';

        $html .= '
                <div style="text-align: center; background-color: #8a2be2; color: white; font-size: 18px; font-weight: bold; padding: 5px; margin-top: 20px;">
                    DETAILS
                </div>';

        $html .= '
        <div style="
            text-align: center; 
            background: linear-gradient(to right, #8a2be2, #512b58); 
            color: white; 
            font-size: 20px; 
            font-weight: bold; 
            padding: 15px; 
            margin: 20px 0; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        ">
            <span style="color: #ffcc00;">Balance Amount:</span> 
            <span style="font-size: 24px; color: #00ffcc;">' . number_format($balanceAmount, 2) . '</span>
        </div>';

        $html .= '
                <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 14px; margin-top: 10px;">
                    <thead>
                        <tr style="background: linear-gradient(to right, #d88aff, #e5b9f2); color: black; border: 3px solid white;">
                            <th style="padding: 10px; border: 3px solid white; width: 6%; font-weight: normal;">Serial No</th>
                            <th style="padding: 10px; border: 3px solid white; width: 20%; font-weight: normal;">Paid Amount</th>
                            <th style="padding: 10px; border: 3px solid white; width: 20%; font-weight: normal;">Payment Mode</th>
                            <th style="padding: 10px; border: 3px solid white; width: 20%; font-weight: normal;">Payment Status</th>
                            <th style="padding: 10px; border: 3px solid white; width: 20%; font-weight: normal;">Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>';

        $srNo = 1;
        foreach ($contractPaymentDetails as $paymentDetail) {
            $html .= '
                    <tr style="background: linear-gradient(to right, #f8e6ff, #e5b9f2); color: black; font-weight: bold; border: 3px solid white;">
                        <td style="padding: 10px; border: 3px solid white;">' . $srNo++ . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . htmlspecialchars($paymentDetail->payment_amount) . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . htmlspecialchars($paymentDetail->payment_method) . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . htmlspecialchars($paymentDetail->payment_status) . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . htmlspecialchars($paymentDetail->payment_date) . '</td>
                    </tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div></body></html>';

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($html);

        // File name for the invoice
        $filename = "Payment_Invoice_" . $contractId . ".pdf";
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }
}
