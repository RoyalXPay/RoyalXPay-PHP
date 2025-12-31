<?php

namespace App\Controllers;

use App\Models\CompanyInfoModel;
use App\Controllers\BaseController;

class CompanyInfoController extends BaseController
{

    public function index()
    {
        $companyInfoModel = new CompanyInfoModel();

        // fetch existing company info
        $data = [];
        $data['company_info'] = $companyInfoModel->first();

        return view('company_info', $data);
    }

    public function store()
    {
        $session = session();
        $companyInfoModel = new CompanyInfoModel();

        // Get the submitted data
        $data = $this->request->getPost();

        // Fetch existing company info to get current image names
        $companyInfo = $companyInfoModel->find(1);

        // Prepare the data for either create or update
        $updateData = [
            'citylight_address' => $data['citylight_address'],
            'contact_details' => $data['contact_details'],
            'citylight_email' => $data['citylight_email'],
            'bank_details' => $data['bank_details'],
            'return_policies' => $data['return_policies'],
        ];

        // Create uploads directory if it doesn't exist
        if (!is_dir(FCPATH . 'uploads/company')) {
            mkdir(FCPATH . 'uploads/company', 0777, true);
        }

        $citylightLogo = $this->request->getFile('citylight_logo');
        if ($citylightLogo->isValid() && !$citylightLogo->hasMoved()) {
            // Handle image upload
            $logoName = $citylightLogo->getRandomName();
            if ($citylightLogo->move(FCPATH . 'uploads/company/', $logoName)) {
                // Remove the previous image on edit
                if (!empty($companyInfo['citylight_logo'])) {
                    $previousImage = $companyInfo['citylight_logo'];
                    if ($previousImage && file_exists(FCPATH . 'uploads/company/' . $previousImage)) {
                        unlink(FCPATH . 'uploads/company/' . $previousImage);
                    }
                }

                $updateData['citylight_logo'] = $logoName;
            } else {
                // Log or handle the error if the file move fails
                log_message('error', 'Failed to move uploaded file: ' . $citylightLogo->getErrorString());
            }
        } else {
            // Log or handle the error for invalid file upload
            log_message('error', 'File upload error: ' . $citylightLogo->getErrorString());
        }

        // Handle signature upload
        $signature = $this->request->getFile('signature');
        if ($signature && $signature->isValid() && !$signature->hasMoved()) {
            // Remove existing signature if it exists
            if (!empty($companyInfo['signature'])) {
                $existingSignaturePath = FCPATH . 'uploads/company/' . $companyInfo['signature'];
                if (file_exists($existingSignaturePath)) {
                    unlink($existingSignaturePath);
                }
            }

            $signatureName = $signature->getRandomName();
            if ($signature->move(FCPATH . 'uploads/company', $signatureName)) {
                $updateData['signature'] = $signatureName;
            } else {
                // Log or handle the error if the file move fails
                log_message('error', 'Failed to move uploaded signature file: ' . $signature->getErrorString());
            }
        } else {
            // Log or handle the error for invalid file upload
            log_message('error', 'Signature upload error: ' . ($signature ? $signature->getErrorString() : 'No file uploaded'));
        }

        // Handle stamp upload
        $stamp = $this->request->getFile('stamp');
        if ($stamp && $stamp->isValid() && !$stamp->hasMoved()) {
            // Remove existing stamp if it exists
            if (!empty($companyInfo['stamp'])) {
                $existingStampPath = FCPATH . 'uploads/company/' . $companyInfo['stamp'];
                if (file_exists($existingStampPath)) {
                    unlink($existingStampPath);
                }
            }

            $stampName = $stamp->getRandomName();
            if ($stamp->move(FCPATH . 'uploads/company', $stampName)) {
                $updateData['stamp'] = $stampName;
            } else {
                // Log or handle the error if the file move fails
                log_message('error', 'Failed to move uploaded stamp file: ' . $stamp->getErrorString());
            }
        } else {
            // Log or handle the error for invalid file upload
            log_message('error', 'Stamp upload error: ' . ($stamp ? $stamp->getErrorString() : 'No file uploaded'));
        }

        // Check if company info exists to decide whether to create or update
        if ($companyInfo) {
            // Update the company information in the database
            $companyInfoModel->update(1, $updateData);
            $session->setFlashdata('success', 'Company information updated successfully.');
        } else {
            // Create a new company information record
            $companyInfoModel->save($updateData);
            $session->setFlashdata('success', 'Company information saved successfully.');
        }

        return redirect()->to(site_url('company-info'));
    }
}
