<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $template;
    protected $data = [];
	protected $helpers = ['CheckLoginHelper', 'CustomfunctionsHelper','SeoHelper'];
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

  protected function initializePrivileges()
{
    $userId = session('user_id');
    $PrivilegeModel = new \App\Models\PrivilegeModel();
    $privileges = $PrivilegeModel->where('merchant_id', $userId)->findAll();

    $moduleAccess = [];
    $subModuleAccess = [];
    $subSubModuleAccess = [];

    $moduleModel = new \App\Models\ModuleModel();
    $subModuleModel = new \App\Models\SubmoduleModel();
    $subSubModuleModel = new \App\Models\SubSubmoduleModel();

    foreach ($privileges as $p) {
        if ($p['can_add'] || $p['can_edit'] || $p['can_delete'] || $p['can_view'] || $p['can_download']) {

            if (!empty($p['module_id'])) {
                $mod = $moduleModel->find($p['module_id']);
                if ($mod) {
                    $moduleAccess[] = strtolower($mod['name']);
                }
            }

            if (!empty($p['submodule_id'])) {
                $sub = $subModuleModel->find($p['submodule_id']);
                if ($sub) {
                    $subModuleAccess[] = strtolower($sub['name']);
                }
            }

            if (!empty($p['sub_submodule_id'])) {
                $ssub = $subSubModuleModel->find($p['sub_submodule_id']);
                if ($ssub) {
                    $subSubModuleAccess[] = strtolower($ssub['name']);
                }
            }
        }
    }

    // Remove duplicates
    $moduleAccess = array_unique($moduleAccess);
    $subModuleAccess = array_unique($subModuleAccess);
    $subSubModuleAccess = array_unique($subSubModuleAccess);

    session()->set('module_access', $moduleAccess);
    session()->set('submodule_access', $subModuleAccess);
    session()->set('subsubmodule_access', $subSubModuleAccess);
}


}
