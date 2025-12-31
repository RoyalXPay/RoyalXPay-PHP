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

// $routes->get('contact-us', 'PageController::contact');
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
$routes->post('api/general/merchant-wallet-balance', 'Api\GeneralApiController::getMerchantWalletBalance');

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

// //Notifications routes start
// $routes->group('notifications', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/', 'NotificationController::index');
//     $routes->post('save', 'NotificationController::save');
//     $routes->get('edit/(:num)', 'NotificationController::edit/$1');
//     $routes->get('delete/(:num)', 'NotificationController::delete/$1');
//     $routes->get('preview/(:num)', 'NotificationController::showDetails/$1');
// });
//start Frontend
$routes->get('search-page', 'Frontend::searchPage');
$routes->get('menu-page', 'Frontend::menupage');
$routes->get('recharge', 'Frontend::recharge');
$routes->get('contact-us', 'Frontend::contactus');
$routes->get('contract_us', 'Frontend::contactus');
$routes->get('blog', 'Frontend::index');
$routes->get('search-page.html', 'Frontend::searchPage');
$routes->get('menu-page.html', 'Frontend::menupage');
$routes->get('recharge.html', 'Frontend::recharge');
$routes->get('contact-us.html', 'Frontend::contactus');

$routes->get('discount-loyalities', 'Frontend::discountloyalities');
$routes->get('company', 'Frontend::company');
$routes->get('payment-services', 'Frontend::paymentservices');
$routes->get('recharge-bill', 'Frontend::rechargebill');
$routes->get('wealth', 'Frontend::wealth');
$routes->get('discount-loyalities.html', 'Frontend::discountloyalities');
$routes->get('company.html', 'Frontend::company');
$routes->get('payment-services.html', 'Frontend::paymentservices');
$routes->get('recharge-bill.html', 'Frontend::rechargebill');
$routes->get('wealth.html', 'Frontend::wealth');

//end Frontend
$routes->get('index', 'Frontend::index');

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

$routes->group('api/addc-payment', function($routes) {
    $routes->get('/', 'Api\AddcPayment::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\AddcPayment::getToken'); // Token generation
    $routes->post('make-payment', 'Api\AddcPayment::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\AddcPayment::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\AddcPayment::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\AddcPayment::transactionReport'); // For reports/status
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


    $routes->get('api/addc/reports', 'Api\Addc::reports');


    $routes->group('api/addc', function($routes) {
    $routes->get('/', 'Api\Addc::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Addc::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Addc::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Addc::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Addc::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Addc::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Addc::reports');
});


    $routes->get('api/addc/reports', 'Api\Addc::reports');

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
$routes->post('registerSave', 'AuthController::registerSave');
$routes->get('customers/payment_history/(:num)', 'CustomerController::payment_history/$1');
$routes->post('send-otp', 'AuthController::sendOtp');
$routes->post('verify-otp', 'AuthController::verifyOtp');

//aadc
$routes->get('api/aadc/receipt/(:num)', 'Api\Aadc::receipt/$1');
$routes->get('api/aadc/receipt/(:segment)', 'Api\Aadc::receipt/$1');
    $routes->get('api/aadc/receipt/(:num)', 'Api\Aadc::receipt/$1');

    //addc
    $routes->get('api/addc/receipt/(:num)', 'Api\Addc::receipt/$1');
$routes->get('api/addc/receipt/(:segment)', 'Api\Addc::receipt/$1');
    $routes->get('api/addc/receipt/(:num)', 'Api\Addc::receipt/$1');

    //nol topup
    $routes->get('api/noltopup/reports', 'Api\Noltopup::reports');


    $routes->group('api/noltopup', function($routes) {
    $routes->get('/', 'Api\Noltopup::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Noltopup::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Noltopup::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Noltopup::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Noltopup::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Noltopup::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Noltopup::reports');
    
});

 $routes->get('api/noltopup/receipt/(:num)', 'Api\Noltopup::receipt/$1');
$routes->get('api/noltopup/receipt/(:segment)', 'Api\Noltopup::receipt/$1');
    $routes->get('api/noltopup/receipt/(:num)', 'Api\Noltopup::receipt/$1');


//end nol topup

//national bond
    $routes->get('api/nationalbond/reports', 'Api\Nationalbond::reports');


    $routes->group('api/nationalbond', function($routes) {
    $routes->get('/', 'Api\Nationalbond::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Nationalbond::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Nationalbond::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Nationalbond::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Nationalbond::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Nationalbond::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Nationalbond::reports');
    
});

 $routes->get('api/nationalbond/receipt/(:num)', 'Api\Nationalbond::receipt/$1');
$routes->get('api/nationalbond/receipt/(:segment)', 'Api\Nationalbond::receipt/$1');
    $routes->get('api/nationalbond/receipt/(:num)', 'Api\Nationalbond::receipt/$1');


//end national bond

//du topup
    $routes->get('api/dutopup/reports', 'Api\Dutopup::reports');


    $routes->group('api/dutopup', function($routes) {
    $routes->get('/', 'Api\Dutopup::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Dutopup::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Dutopup::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Dutopup::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Dutopup::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Dutopup::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Dutopup::reports');
    
});

 $routes->get('api/dutopup/receipt/(:num)', 'Api\Dutopup::receipt/$1');
$routes->get('api/dutopup/receipt/(:segment)', 'Api\Dutopup::receipt/$1');
    $routes->get('api/dutopup/receipt/(:num)', 'Api\Dutopup::receipt/$1');


//end du topup

//tahseel
    $routes->get('api/tahseel/reports', 'Api\Tahseel::reports');


    $routes->group('api/tahseel', function($routes) {
    $routes->get('/', 'Api\Tahseel::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Tahseel::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Tahseel::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Tahseel::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Tahseel::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Tahseel::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Tahseel::reports');
    
});

 $routes->get('api/tahseel/receipt/(:num)', 'Api\Tahseel::receipt/$1');
$routes->get('api/tahseel/receipt/(:segment)', 'Api\Tahseel::receipt/$1');
    $routes->get('api/tahseel/receipt/(:num)', 'Api\Tahseel::receipt/$1');


//end tahseel

//tahseel
    $routes->get('api/salikdirect/reports', 'Api\Salikdirect::reports');


    $routes->group('api/salikdirect', function($routes) {
    $routes->get('/', 'Api\Salikdirect::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Salikdirect::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Salikdirect::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Salikdirect::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Salikdirect::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Salikdirect::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Salikdirect::reports');
    
});

 $routes->get('api/salikdirect/receipt/(:num)', 'Api\Tahseel::receipt/$1');
$routes->get('api/salikdirect/receipt/(:segment)', 'Api\Tahseel::receipt/$1');
    $routes->get('api/salikdirect/receipt/(:num)', 'Api\Tahseel::receipt/$1');


//end tahseel

//internation recharge
    $routes->get('api/internationrecharge/reports', 'Api\Internationrecharge::reports');


    $routes->group('api/Internationrecharge', function($routes) {
    $routes->get('/', 'Api\Internationrecharge::index'); // Loads the HTML form (View)

    $routes->post('get-token', 'Api\Internationrecharge::getToken'); // Token generation
    $routes->post('make-payment', 'Api\Internationrecharge::makePayment'); // Main Payment API

    $routes->post('fetch-bill', 'Api\Internationrecharge::fetchBill'); // Optional if you add fetch bill separately
    $routes->get('balance', 'Api\Internationrecharge::walletBalance'); // Optional for wallet balance (merchant)
    $routes->post('transaction-report', 'Api\Internationrecharge::transactionReport'); // For reports/status
    $routes->get('reports', 'Api\Internationrecharge::reports');
    
});

 $routes->get('api/internationrecharge/receipt/(:num)', 'Api\Internationrecharge::receipt/$1');
$routes->get('api/internationrecharge/receipt/(:segment)', 'Api\Internationrecharge::receipt/$1');
    $routes->get('api/internationrecharge/receipt/(:num)', 'Api\Internationrecharge::receipt/$1');


//end internation recharge
$routes->get('manage-wallet/recharge', 'WalletController::rechargeForm');
$routes->post('manage-wallet/recharge', 'WalletController::saveRecharge');
$routes->get('manage-wallet/recharge-history', 'WalletController::rechargeHistory');
$routes->get('notifications', 'WalletController::fetchNotifications');
$routes->get('wallet/mark-done/(:num)', 'WalletController::markNotificationDone/$1');
$routes->post('notifications/mark-done/(:num)', 'WalletController::markNotificationDone/$1');
$routes->post('wallet/approve-recharge/(:num)', 'WalletController::approveRecharge/$1');
$routes->post('wallet/approve-recharge/(:num)', 'Admin\WalletController::approveRecharge/$1');

