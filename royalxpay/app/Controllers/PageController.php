<?php

namespace App\Controllers;

use App\Models\ContactUsModel;
use App\Controllers\BaseController;

class PageController extends BaseController
{
    public function contact()
    {
        set_title('Contract Us | ' . SITE_NAME);

        $data = [];
        // Load the view
        return view('frontend/contract_us', $data);
    }

    public function submitContactForm()
    {
        $contactModel = new ContactUsModel();

        $data = [
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'phone'   => $this->request->getPost('phone'),
            'message' => $this->request->getPost('message'),
        ];

        if ($contactModel->insert($data)) {
            return redirect()->back()->with('success', 'Thank you for contacting us. We will get back to you soon!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
