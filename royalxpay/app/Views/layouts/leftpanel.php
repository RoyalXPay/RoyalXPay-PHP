<!-- ========== Left Sidebar Start ========== -->
<?php
$moduleAccess = session()->get('module_access') ?? [];

// Grant all access if the user is Superadmin
if (strtolower(session()->get('user_type')) === 'superadmin') {
    $moduleAccess = [
        'customers',
        'Banking',
        'Financial',
        'Bill & Recharge',
        'Subscription',
        'Notification',
        'EMS',
        'Employee Privilege',
         'Merchant',
         'Reminders',
        'reports',
           'Payment',
    ];
}else if (strtolower(session()->get('user_type')) === 'Merchant') {
    $moduleAccess = [
        'customers',

         'Reminders',
        'Payment',
    ];
}else{
    
    $moduleAccess = [
        'customers',
                'EMS',

         'Reminders',
        'Payment',
    ];

}
?>
<style>
    #toolbarContainer{
    display: none;
}
    </style>
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="<?= site_url('admin/dashboard'); ?>">
                        <span class="badge rounded-pill bg-primary float-end" key="t-hot">2</span>
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Dashboard</span>
                    </a>
                </li>


                <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-user"></i>
                            <span key="t-customers">Customers</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="<?= site_url('customers'); ?>">
                                    All Customer
                                </a>
                            </li>
                            <!-- <li>
                                <a href="<?= site_url('paid-customers'); ?>">
                                    Subscribed Customer
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('paid-customers'); ?>">
                                    Paid Customers
                                </a>
                            </li> -->
                        </ul>
                    </li>
                    
                <?php endif; ?>
                 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin'])): ?>
                    <li>
                        <a href="<?= site_url('merchant'); ?>" class="waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-reportsMenu">Merchant</span>
                        </a>
                        
                    </li>
                <?php endif; ?>
                  <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','admin'])): ?>
                    <li>
                        <a href="<?= site_url('manage-wallet'); ?>" class="waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-reportsMenu">Manage Wallet</span>
                        </a>
                     
                    </li>
                <?php endif; ?>
                <?php if (in_array(strtolower(session()->get('user_type')), [ 'merchant','Merchant'])):  $userId = session()->get('user_id'); ?>
                    <li>
                       <a class="waves-effect" href="<?= site_url('wallet-history/' . $userId); ?>">
                            <i class="fas fa-history"></i> Wallet History
                      </a>
                     
                    </li>
                <?php endif; ?>
                <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>

    <li>
    <a href="#" class="has-arrow waves-effect">
        <i class="bx bx-credit-card"></i>
        <span key="t-payments">Recharge & Bill Payments</span>
    </a>
    <ul class="sub-menu" aria-expanded="false">
       
        <li>
            <a href="#" class="has-arrow">Utilities Payment</a>
            <ul class="sub-menu" aria-expanded="false">
                 <li><a href="<?= site_url('api/aadc'); ?>">AADC</a></li>
                <li><a href="<?= site_url('Api/addc'); ?>">ADDC</a></li>
                <li><a href="#">Electricity</a></li>
                <li><a href="#">Credit Card Bill</a></li>
                <li><a href="#">Loan Repayment</a></li>
                <li><a href="#">Rent Via Credit card</a></li>
                <li><a href="#">Book Cylinder</a></li>
                <li><a href="#">broadband/Landline</a></li>
                <li><a href="#">fees via Credit card</a></li>
                <li><a href="#">Prepaid Meter</a></li>
                <li><a href="#">Piped gas</a></li>
                <li><a href="#">Eduction fees</a></li>
                <li><a href="#">Water bill</a></li>
                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Topup</a>
            <ul class="sub-menu" aria-expanded="false">
                 <li><a href="#">Dou Topup</a></li>
                <li><a href="#">Nol Topup</a></li>
                <li><a href="#">National Bond</a></li>
                <li><a href="#">Tahseel</a></li>
                <li><a href="#">Mobile postpaid</a></li>
                <li><a href="#">Mobile prepaid</a></li>

                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Transportation</a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="#">Salik Direct</a></li>
               
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">International Topup</a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="#">International Recharge</a></li>
                
                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Telecomunication</a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="#">Du Postpaid</a></li>
                <li><a href="#">Du prepaid</a></li>
                <li><a href="#">Salik direct</a></li>
                
             
                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Other</a>
            <ul class="sub-menu" aria-expanded="false">
                 
                <li><a href="#">Dubai Police</a></li>
                <li><a href="#">etisalat</a></li>
                <li><a href="#">fewa</a></li>
                <li><a href="#">hafilat</a></li>
                <li><a href="#">MAWAQIF PVT</a></li>
                <li><a href="#">Online charity</a></li>
                <li><a href="#">Sergas</a></li>
                <li><a href="#">Upay</a></li>
                <li><a href="#">Donation</a></li>
                <li><a href="#">Donation</a></li>

                <li><a href="#">Devotion</a></li>

                <li><a href="#">Hospitals</a></li>


                
            </ul>
        </li>
    </ul>
</li>
<?php endif; ?>
 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="#" class="has-arrow waves-effect">
                            <i class="bx bx-transfer-alt"></i>
                            <span key="t-banking">Banking</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="#">
                                    Transfer to Bank & Self A/c
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Transfer to Mobile number
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Transaction History
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>


 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="#" class="has-arrow waves-effect">
                            <i class="bx bx-briefcase"></i>
                            <span key="t-financial">Finance</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="#" class="has-arrow">Accounts</a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="#">Saving Account</a></li>
                                    <li><a href="#">Current Account</a></li>
                                    <li><a href="#">Deposits</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="has-arrow">NFBC Fixed Deposit</a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="#">Credit Card</a></li>
                                    <li><a href="#">Loan Services</a></li>
                                    <li><a href="#">Credit Lines</a></li>
                                    <li><a href="#">Personal Loan</a></li>
                                    <li><a href="#">Demat Accounts</a></li>
                                    <li><a href="#">Insurance</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="has-arrow">General Insurance</a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="#">Health Insurance</a></li>
                                    <li><a href="#">Vehicle Insurance</a></li>
                                    <li><a href="#">Accidental / Death Insurance</a></li>
                                    <li><a href="#">Home / Shop / Office Protection Insurance</a></li>
                                    <li><a href="#">Job Loss Insurance</a></li>
                                    <li><a href="#">Cyber Fraud Insurance</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="has-arrow">Life Insurance</a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="#">Traditional & Term Insurance</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="has-arrow">Investments</a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="#">Mutual Fund</a></li>
                                    <li><a href="#">Direct Equity</a></li>
                                    <li><a href="#">Unlisted Equity</a></li>
                                    <li><a href="#">Bonds</a></li>
                                    <li><a href="#">Debenture</a></li>
                                    <li><a href="#">Alternative Investment</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

               
 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-reportsMenu">EMS</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="<?= site_url('employees'); ?>" class="waves-effect">
                                    <i class="bx bx-user"></i>
                                    <span key="t-customer-report">Employees</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('employee-salaries'); ?>" class="waves-effect">
                                    <i class="bx bx-package"></i>
                                    <span key="t-rent-product">Employees Salary</span>
                                </a>
                            </li>
                             <li>
                                <a href="<?= site_url('employee-tasks'); ?>" class="waves-effect">
                                    <i class="bx bx-package"></i>
                                    <span key="t-rent-product">Asign Task</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (in_array('Subscription', $moduleAccess)): ?>
                    <li>
                        <a href="#" class="waves-effect">
                            <i class="bx bx-shopping-bag"></i>
                            <span key="t-purchases">Subscription</span>
                        </a>
                    </li>
                <?php endif; ?>

                              


 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="#" class="waves-effect">
                            <i class="bx bx-cart"></i>
                            <span key="t-rentMenu">Notification</span>
                        </a>
                     
                    </li>
                <?php endif; ?>

              


 

               
 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="<?= site_url('#'); ?>" class=" waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-reportsMenu">Reminders</span>
                        </a>
                        
                    </li>
                <?php endif; ?>
             
<!-- Payments -->

<?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-file"></i>
                            <span key="t-reportsMenu">Reports</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="#" class="waves-effect">
                                    <i class="bx bx-user"></i>
                                    <span key="t-customer-report">Customer Reports</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="waves-effect">
                                    <i class="bx bx-package"></i>
                                    <span key="t-rent-product">Product Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

               
              
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->