<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;

use App\Models\UserModel;
use CodeIgniter\Session\Session;
use App\Libraries\Paginationnew;
use App\Controllers\BaseController;

class Settings extends BaseController
{


    protected $session;
    protected $adminModel;

    public function __construct()
    {
        $this->session = session();
    }

    public function index()
{
   
    set_title('Settings | ' . SITE_NAME);

    $data = [
        'pagetitle' => "Manage Settings & Privileges",
        'action' => "settings"
    ];

    return view('settings/index', $data);
    // Render the view with buttons
}


    public function indexold()
    {

        if (!$this->isAdminLoggedIn) {
            return redirect()->to(site_url('admin'));
        }

        set_title('Welcome | ' . SITE_NAME);

        $data = array();

        set_title('Welcome | ' . SITE_NAME);

        $data['pagetitle'] = "Setting List";
        $data['action'] = "settings";
        $txtsearch = $this->request->getGet('txtsearch');
        $searchArray = array();
        $paginationnew = new Paginationnew();

        if ($txtsearch) {
            $searchArray['txtsearch'] = $txtsearch;
        }
        $page = $this->request->getGet('page');
        $page = $page ? $page : 1;
        $Limit = PER_PAGE_RECORD;
        $totalRecord = $this->settingsModel->getData($searchArray, '', '', 1); // return count value

        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;

        $pagination = $paginationnew->getPaginate($totalRecord, $page, $Limit);
        $data['txtsearch'] = $txtsearch;
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $pagination;
        $data["settingData"] = $this->settingsModel->getData($searchArray, $startLimit, $Limit);
        $data["searchArray"] = $searchArray;

        $this->template->render('admintemplate', 'contents', 'admin/settings/list', $data);
    }

    public function editsetting()
    {
        if (!$this->isAdminLoggedIn) {
            return redirect()->to(site_url('admin'));
        }
        set_title('Welcome | ' . SITE_NAME);

        $data['pagetitle'] = "Edit Setting";

        $id = $this->request->getGet('id');
        if ($id) {
            $settingsModel = new SettingsModel();
            $data['id'] = $id;
            $data['settingDetails'] = $settingsModel->asArray()->where(['id' => $id])->first();

            //           print_r($data);die;
        } else {
            return redirect()->to(site_url('newaccount'));
        }

        //print_r($data['adminDetails']);die;
        $this->template->render('admintemplate', 'contents', 'admin/settings/editsetting', $data);
    }




    public function updatesetting()
    {
        $data = array();
        $response = array("status" => false, "error" => "", "message" => '', "data" => "");

        $postdata = $this->request->getPost();
        $uploadedfile = $this->request->getFiles('filename');


        $extc = '';


        $newfilenameName = '';

        if (isset($uploadedfile['filename']) && $uploadedfile['filename']) {
            if ($uploadedfile['filename']->getName()) {
                $orderFolderpath = PUBLIC_PATH . 'images/';

                if (!is_dir($orderFolderpath)) {
                    mkdir($orderFolderpath, 0777);
                    chmod($orderFolderpath, 0777);
                }

                $fileObject = $uploadedfile['filename'];

                $newfilenameName = $fileObject->getName();
                $extc     = $fileObject->guessExtension();


                if (!$fileObject->isValid()) {
                    $response['error'] = $file->getErrorString() . '-' . $file->getError();
                } else {
                    $fileObject->move($orderFolderpath, $newfilenameName);
                    $newfilenameName = $fileObject->getName();
                }
                $udateArray['varvalue'] = $newfilenameName;
                $this->settingsModel->set($udateArray)->where("id", $postdata['id'])->update();

                $this->session->setFlashdata('message', 'Updated  successfully.');
            }
        } else if (count($postdata)) {
            foreach ($postdata as $keyname => $kevalue) {
                $udateArray['varvalue'] = $kevalue;
                $this->settingsModel->set($udateArray)->where("name", $keyname)->update();

                $this->session->setFlashdata('message', 'Updated  successfully.');
            }
        }

        return redirect()->to(site_url('settings'));
    }


    public function removesetting()
    {
        $data = array();
        $response = array("status" => false, "error" => "", "message" => '', "data" => "");

        $id = $this->request->getGet('id');
        $arrSearch = array();
        if ($id) {
            $arrSearch['id'] = $id;
            $settingdata = $this->settingsModel->getData($arrSearch);
            $settingdata = isset($settingdata[0]) ? $settingdata[0] : array();

            if ($settingdata->st_type == 'file') {
                $orderFolderpath = PUBLIC_PATH . 'images/' . $settingdata->varvalue;
                @unlink($orderFolderpath);
            }
            $udateArray['varvalue'] = "";
            $this->settingsModel->set($udateArray)->where("id", $id)->update();
            $response['status'] = 201;
            $this->session->setFlashdata('message', 'Deleted  successfully.');
        } else {
            $this->session->setFlashdata('errmessage', 'Opps some error.');
        }

        return redirect()->to(site_url('settings'));
    }
}