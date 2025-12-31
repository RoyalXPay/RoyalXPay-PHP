<?php

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Set default controller and method
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('index');
// $routes->set404Override('App\Controllers\Error404::index');

/*
| --------------------------------------------------------------------
| FRONTEND ROUTES
| --------------------------------------------------------------------
*/

$routes->get('/', 'HomeController::index');
$routes->get('products/rent', 'HomeController::showRentProducts');
$routes->get('products/sale', 'HomeController::showSaleProducts');
$routes->get('products/details/(:segment)', 'HomeController::showProductDetail/$1');
$routes->get('sale/products/details/(:segment)', 'HomeController::saleProductDetails/$1');

$routes->get('contact-us', 'PageController::contact');
$routes->post('save-contact', 'PageController::submitContactForm');

$routes->post('login/verify', 'AuthenticationController::verifyLogin');
// $routes->post('register', 'AuthenticationController::register');
$routes->get('logout', 'AuthenticationController::logout');

$routes->get('cart', 'CartController::index');
$routes->post('cart/add', 'CartController::add');
$routes->post('cart/update/(:any)', 'CartController::update/$1');
$routes->get('cart/remove/(:any)', 'CartController::remove/$1');

$routes->get('wishlist', 'HomeController::wishlist');
$routes->get('checkout', 'CheckoutController::index');
$routes->post('checkout/placeOrder', 'CheckoutController::placeOrder');
$routes->get('checkout/success/(:num)', 'CheckoutController::success/$1');

$routes->group('user', function ($routes) {
    $routes->get('dashboard', 'UserDashboardController::index');
    $routes->get('profile', 'UserDashboardController::profile');
    $routes->post('profile/update', 'UserDashboardController::updateProfile');

    $routes->get('orders', 'UserDashboardController::orders');
    $routes->get('orders/view/(:num)', 'UserDashboardController::viewOrderDetails/$1');

    $routes->get('logout', 'AuthenticationController::logout');
    $routes->get('profile', 'AuthenticationController::profile');
    $routes->get('change-password', 'AuthenticationController::changePassword');
    $routes->post('update-password', 'AuthenticationController::UpdatePassword');
});

$routes->get('login', 'AuthController::index');
$routes->get('generate-pdf', 'PdfController::generate');
$routes->post('verify-login', 'AuthController::verifyLogin');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('settings', 'Admin\Settings::index');
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('profile', 'AuthController::profile');
    $routes->get('change-password', 'AuthController::changePassword');
    $routes->post('update-password', 'AuthController::UpdatePassword');
});

$routes->group('customers', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CustomerController::index'); // All Customer
    $routes->get('add', 'CustomerController::add');
    $routes->post('save', 'CustomerController::save');
    $routes->get('edit/(:num)', 'CustomerController::edit/$1');
    $routes->post('delete', 'CustomerController::delete');
    $routes->get('preview/(:num)', 'CustomerController::show/$1');
    $routes->post('add-payment', 'CustomerController::add_amount');
$routes->get('paid-customers', 'CustomerController::paidCustomers');
    // Payments
    $routes->get('payment-history/(:num)', 'CustomerPaymentController::history/$1');
    // $routes->get('add-payment/(:num)', 'CustomerPaymentController::add/$1');
    $routes->post('save-payment', 'CustomerPaymentController::save');
    $routes->get('invoice/(:num)', 'CustomerPaymentController::invoice/$1');
});

$routes->group('merchant', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'MerchantController::index');
    $routes->post('save', 'MerchantController::save');
    $routes->get('edit/(:num)', 'MerchantController::edit/$1');
    $routes->post('delete', 'MerchantController::delete');
    $routes->get('preview/(:num)', 'MerchantController::showDetails/$1');
});

$routes->group('employee-tasks', function ($routes) {

    // Task List (Index)
    $routes->get('/', 'EmployeeTaskController::index');

    // Create Task Page
    $routes->get('create', 'EmployeeTaskController::create');

    // Edit Task Page (with query parameter)
    $routes->get('edit', 'EmployeeTaskController::edit');

    // Save Task (Add or Update)
    $routes->post('save', 'EmployeeTaskController::save');

    // Preview Task Details
    $routes->get('preview', 'EmployeeTaskController::preview');

    // Update Task Status (via POST)
    $routes->post('update-status', 'EmployeeTaskController::updateStatus');

    // Delete Task (via query parameter)
    $routes->post('delete', 'EmployeeTaskController::delete');
});

$routes->group('employee-salaries', function ($routes) {

    // List all salaries with optional filters (index)
    $routes->get('/', 'EmployeeSalaryController::index');

    // Show form to add salary, optional employeeId to prefill
    $routes->get('create', 'EmployeeSalaryController::create');

    // Save new or update salary (POST)
    $routes->post('store', 'EmployeeSalaryController::store');

    // Show edit form for a salary by salaryId
    $routes->get('edit/(:num)', 'EmployeeSalaryController::edit/$1');

    // Delete salary (POST request)
    $routes->post('delete', 'EmployeeSalaryController::delete');

    // View salary details
    $routes->get('view/(:num)', 'EmployeeSalaryController::view/$1');
});



$routes->group('subscription', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SubscriptionController::index');
    $routes->post('save', 'SubscriptionController::save');
    $routes->get('edit/(:num)', 'SubscriptionController::edit/$1');
    $routes->get('delete/(:num)', 'SubscriptionController::delete/$1');
});

//Notifications routes start
$routes->group('notifications', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->post('save', 'NotificationController::save');
    $routes->get('edit/(:num)', 'NotificationController::edit/$1');
    $routes->get('delete/(:num)', 'NotificationController::delete/$1');
    $routes->get('preview/(:num)', 'NotificationController::showDetails/$1');
});

$routes->group('banking', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Banking\BankingController::index');
    $routes->get('transfer-to-bank', 'Banking\BankingController::transferToBank');
    $routes->get('transfer-to-mobile', 'Banking\BankingController::transferToMobile');
    $routes->get('transaction-history', 'Banking\BankingController::transactionHistory');
});

$routes->group('bills', ['filter' => 'auth'], function($routes) {
    $routes->get('electricity', 'Admin\BillsController::electricity');
    $routes->get('mobile-recharge', 'Admin\BillsController::mobileRecharge');
    $routes->get('dth', 'Admin\BillsController::dth');
    $routes->get('fastag', 'Admin\BillsController::fastag');
    $routes->get('metro', 'Admin\BillsController::metro');
    // ...add other routes similarly
});



$routes->group('product-master', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'ProductController::index');
    $routes->post('create', 'ProductController::create');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('update', 'ProductController::update');
    $routes->get('preview/(:num)', 'ProductController::showDetails/$1');
    $routes->get('delete/(:num)', 'ProductController::delete/$1');
});

$routes->get('get-products-details', 'ProductController::getProductsRows/$1');

$routes->group('rent-products', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'RentProductController::index');
    $routes->post('save', 'RentProductController::store');
    $routes->get('create', 'RentProductController::create');
    $routes->get('edit/(:num)', 'RentProductController::edit/$1');
    $routes->get('delete/(:num)', 'RentProductController::delete/$1');
    $routes->get('preview/(:num)', 'RentProductController::showDetails/$1');
});

$routes->group('sale-products', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'SaleProductController::index');
    $routes->get('create', 'SaleProductController::create');
    $routes->post('save', 'SaleProductController::store');
    $routes->get('edit/(:num)', 'SaleProductController::edit/$1');
    $routes->get('delete/(:num)', 'SaleProductController::delete/$1');
    $routes->get('preview/(:num)', 'SaleProductController::showDetails/$1');
});

$routes->group('companies', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'CompanyController::index');
    $routes->post('save', 'CompanyController::save');
    $routes->get('edit/(:num)', 'CompanyController::edit/$1');
    $routes->post('delete', 'CompanyController::delete');
    $routes->get('preview/(:num)', 'CompanyController::showDetails/$1');
});

$routes->group('employees', function ($routes) {
    $routes->get('/', 'EmployeeController::index');
    $routes->get('add', 'EmployeeController::create');
    $routes->post('save', 'EmployeeController::store');
    $routes->get('edit/(:num)', 'EmployeeController::edit/$1');
    $routes->get('view/(:num)', 'EmployeeController::view/$1');
    $routes->post('delete', 'EmployeeController::delete');
    $routes->get('get-attendance', 'EmployeeController::getAttendance');
});

$routes->group('orders', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OrderController::index');
    $routes->post('save', 'OrderController::save');
    $routes->get('edit/(:num)', 'OrderController::edit/$1');
    $routes->post('delete', 'OrderController::delete');
    $routes->get('preview/(:num)', 'OrderController::viewOrderDetails/$1');
});

$routes->group('purchases', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PurchaseController::index');
    $routes->get('create', 'PurchaseController::create');
    $routes->post('save', 'PurchaseController::save');
    $routes->get('edit/(:num)', 'PurchaseController::edit/$1');
    $routes->post('delete', 'PurchaseController::delete');
    $routes->get('preview/(:num)', 'PurchaseController::showDetails/$1');
});

$routes->group('invoices', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'InvoiceController::index');
    $routes->post('save', 'InvoiceController::save');
    $routes->get('edit/(:num)', 'InvoiceController::edit/$1');
    $routes->post('delete', 'InvoiceController::delete');
    $routes->get('preview/(:num)', 'InvoiceController::showDetails/$1');
});

$routes->group('delivery-times', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DeliveryController::index');
    $routes->post('save', 'DeliveryController::save');
    $routes->get('edit/(:num)', 'DeliveryController::edit/$1');
    $routes->get('delete/(:num)', 'DeliveryController::delete/$1');
});

$routes->group('product-types', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProductTypeController::index');
    $routes->post('save', 'ProductTypeController::save');
    $routes->get('edit/(:num)', 'ProductTypeController::edit/$1');
    $routes->get('delete/(:num)', 'ProductTypeController::delete/$1');
});

$routes->group('colors', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ColorsController::index');
    $routes->post('save', 'ColorsController::save');
    $routes->get('edit/(:num)', 'ColorsController::edit/$1');
    $routes->get('delete/(:num)', 'ColorsController::delete/$1');
});

$routes->group('banks', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'BanksController::index');
    $routes->post('save', 'BanksController::save');
    $routes->get('edit/(:num)', 'BanksController::edit/$1');
    $routes->get('delete/(:num)', 'BanksController::delete/$1');
});

$routes->group('sizes', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SizesController::index');
    $routes->post('save', 'SizesController::save');
    $routes->get('edit/(:num)', 'SizesController::edit/$1');
    $routes->get('delete/(:num)', 'SizesController::delete/$1');
});

$routes->group('touch-types', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TouchTypesController::index');
    $routes->post('save', 'TouchTypesController::save');
    $routes->get('edit/(:num)', 'TouchTypesController::edit/$1');
    $routes->get('delete/(:num)', 'TouchTypesController::delete/$1');
});

$routes->group('resolutions', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ResolutionController::index');
    $routes->post('save', 'ResolutionController::save');
    $routes->get('edit/(:num)', 'ResolutionController::edit/$1');
    $routes->get('delete/(:num)', 'ResolutionController::delete/$1');
});

$routes->group('glass-types', ['filter' => 'auth'], function ($routes) {
    // Display the list of glass types
    $routes->get('/', 'GlassTypesController::index');
    $routes->post('save', 'GlassTypesController::save');
    $routes->get('edit/(:num)', 'GlassTypesController::edit/$1');
    $routes->get('delete/(:num)', 'GlassTypesController::delete/$1');
});

$routes->group('terms-and-conditions', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TermsAndConditionsController::index');
    $routes->post('save', 'TermsAndConditionsController::save');
    $routes->get('edit/(:num)', 'TermsAndConditionsController::edit/$1');
    $routes->get('delete/(:num)', 'TermsAndConditionsController::delete/$1');
});

$routes->group('rent-quotations', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'RentQuotationController::index');
    $routes->get('create', 'RentQuotationController::create');
    $routes->post('save', 'RentQuotationController::store');
    $routes->get('edit/(:num)', 'RentQuotationController::edit/$1');
    $routes->get('delete/(:num)', 'RentQuotationController::delete/$1');
    $routes->get('preview/(:num)', 'RentQuotationController::showDetails/$1');
    $routes->get('generate-quotations/(:num)', 'RentQuotationController::generateQuotation/$1');
    $routes->get('fetch-rent-products', 'RentQuotationController::fetchRentProducts');
});

$routes->group('rent-contracts', ['filter' => 'auth'], function ($routes) {
    // Routes for Rent Contracts
    $routes->get('/', 'RentContractController::index');
    $routes->get('create', 'RentContractController::create');
    $routes->post('save', 'RentContractController::saveContract');
    $routes->get('edit/(:num)', 'RentContractController::edit/$1');
    $routes->get('fetch', 'RentContractController::fetchRentProducts');
    $routes->get('delete/(:num)', 'RentContractController::delete/$1');
    $routes->get('preview/(:num)', 'RentContractController::showDetails/$1');
    $routes->get('generate-pdf/(:num)', 'RentContractController::generatePDF/$1');
    $routes->get('generate-invoice/(:num)', 'RentContractController::generateInvoice/$1');
});

$routes->group('contract-payment-history', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PaymentController::index');
    $routes->post('save', 'PaymentController::save');
    $routes->get('edit/(:num)', 'PaymentController::edit/$1');
    $routes->get('delete/(:num)', 'PaymentController::delete/$1');
});

$routes->get('/generate-invoice/(:num)', 'PaymentController::generateInvoice/$1');

$routes->group('contract-delivery-history', ['filter' => 'auth'], function ($routes) {
    // List deliveries for a contract
    $routes->get('/', 'ContractDeliveryController::index');
    $routes->post('save', 'ContractDeliveryController::save');
    $routes->get('edit/(:num)', 'ContractDeliveryController::edit/$1');
    $routes->get('delete/(:num)', 'ContractDeliveryController::delete/$1');
});

$routes->group('sale-quotations', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'SaleQuotationController::index');
    $routes->get('create', 'SaleQuotationController::create');
    $routes->post('save', 'SaleQuotationController::saveQuotation');
    $routes->get('edit/(:num)', 'SaleQuotationController::edit/$1');
    $routes->get('delete/(:num)', 'SaleQuotationController::delete/$1');
    $routes->get('preview/(:num)', 'SaleQuotationController::showDetails/$1');
    $routes->get('getProductsRows', 'SaleQuotationController::getProductsRows');
    $routes->get('generate-quotations/(:num)', 'SaleQuotationController::generateQuotation/$1');
});

$routes->group('sale-contracts', ['filter' => 'auth'], function ($routes) {
    // Routes for Rent Contracts
    $routes->get('/', 'SaleContractController::index');
    $routes->get('create', 'SaleContractController::create');
    $routes->post('save', 'SaleContractController::saveContract');
    $routes->get('edit/(:num)', 'SaleContractController::edit/$1');
    $routes->get('delete/(:num)', 'SaleContractController::delete/$1');
    $routes->get('preview/(:num)', 'SaleContractController::showDetails/$1');
    $routes->get('getProductsRows', 'SaleContractController::getProductsRows');
    $routes->get('generate-pdf/(:num)', 'SaleContractController::generatePDF/$1');
    $routes->get('generate-invoice/(:num)', 'SaleContractController::generateInvoice/$1');
});

$routes->get('send-notification', 'HomeController::testNotification');

$routes->get('feedback', 'CommonController::feedbacks');
$routes->post('feedback/delete', 'CommonController::deleteFeedback');

$routes->get('company-info', 'CompanyInfoController::index');
$routes->post('save-company-info', 'CompanyInfoController::store');

$routes->group('reports', ['filter' => 'auth'], function ($routes) {
    // List deliveries for a contract
    $routes->get('customer-report', 'ReportsController::index');
    $routes->get('rent-product', 'ReportsController::rentProductReport');
    $routes->get('sale-product', 'ReportsController::saleProductReport');
});

$routes->group('roles', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'RoleController::index');
    $routes->post('save', 'RoleController::save');
    $routes->get('edit/(:num)', 'RoleController::edit/$1');
    $routes->get('delete/(:num)', 'RoleController::delete/$1');
});

$routes->group('modules', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ModuleController::index');
    $routes->post('save', 'ModuleController::save');
    $routes->get('edit/(:num)', 'ModuleController::edit/$1');
    $routes->get('delete/(:num)', 'ModuleController::delete/$1');
    $routes->get('getSubmodules', 'ModuleController::getSubmodules');
});

$routes->group('submodules', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SubmoduleController::index');
    $routes->post('save', 'SubmoduleController::save');
    $routes->get('edit/(:num)', 'SubmoduleController::edit/$1');
    $routes->get('delete/(:num)', 'SubmoduleController::delete/$1');
});

$routes->group('permissions', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PermissionController::index');
    $routes->post('save', 'PermissionController::save');
    $routes->get('edit/(:num)', 'PermissionController::edit/$1');
    $routes->get('delete/(:num)', 'PermissionController::delete/$1');
});


$routes->post('customer-auth/login', 'CustomerAuth::login');
$routes->post('customer-auth/register', 'CustomerAuth::register');
$routes->get('customer-auth/logout', 'CustomerAuth::logout');

// Optional for testing from browser (not needed for AJAX login)
$routes->get('customer-auth/login', 'CustomerAuth::login');

$routes->group('api/aadc-payment', function($routes) {
    $routes->get('/', 'Api\AadcPayment::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\AadcPayment::getToken'); // Token generation
    $routes->post('make-payment', 'Api\AadcPayment::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\AadcPayment::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\AadcPayment::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\AadcPayment::transactionReport'); // For reports/status
});

$routes->group('api/aadc', function($routes) {
    $routes->get('/', 'Api\Aadc::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Aadc::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Aadc::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Aadc::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Aadc::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Aadc::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Aadc::reports');
});

    $routes->get('api/aadc/reports', 'Api\Aadc::reports');

$routes->get('add-wallet', 'WalletController::create');

$routes->get('manage-wallet', 'WalletController::index');
$routes->get('edit-wallet/(:num)', 'WalletController::edit/$1');
$routes->post('update-wallet', 'WalletController::update');
$routes->post('store-wallet', 'WalletController::store');

$routes->group('admin', function ($routes) {
    $routes->post('store-wallet', 'WalletController::store');
});


$routes->get('wallet-history/(:num)', 'MerchantWalletController::history/$1');

$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::registerSave');
