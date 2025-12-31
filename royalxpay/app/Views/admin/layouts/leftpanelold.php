<!-- ========== Left Sidebar Start ========== -->
 <style>
    /* Default icon style */
#sidebar-menu ul li a img {
    width: 19px;
    height: 16px;
    margin-right: 8px;
    filter: brightness(0) saturate(100%) invert(43%) sepia(6%) saturate(687%) hue-rotate(185deg) brightness(92%) contrast(86%);
    transition: filter 0.3s ease;
}

/* On hover → make the icon white */
body[data-sidebar=dark] #sidebar-menu ul li a:hover img {
    filter: brightness(0) invert(1);
}

/* Keep text white on hover (your rule) */
body[data-sidebar=dark] #sidebar-menu ul li a:hover {
    color: #fff;
}
 </style>
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
        'AI Agent',
        'EMS',
        'Employee Privilege',
         'Merchant',
         'AI Reminders',
        'reports',
           'Payment',
    ];
}else if (strtolower(session()->get('user_type')) === 'Merchant') {
    $moduleAccess = [
        'customers',

         'AI Reminders',
        'Payment',
    ];
}else{
    
    $moduleAccess = [
        'customers',
                'EMS',

         'AI Reminders',
        'Payment',
    ];

}
?>
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
    <a href="#" class="has-arrow waves-effect" title="Recharge and Bill Payment Services">
        <i class="bx bx-credit-card"></i>
        <span key="t-payments">Recharge & Bill Payments</span>
    </a>
    <ul class="sub-menu" aria-expanded="false">
       
        <li>
            <a href="#" class="has-arrow">Utilities Payment</a>
            <ul class="sub-menu" aria-expanded="false">
                 <li><a href="<?= site_url('api/aadc'); ?>" title="Al Ain Distribution Company"  data-bs-toggle="tooltip" data-bs-placement="right">AADC</a></li>
                <li><a href="<?= site_url('api/addc'); ?>" title="Abu Dhabi Distribution Company"  data-bs-toggle="tooltip" data-bs-placement="right">ADDC</a></li>
              
                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Topup</a>
            <ul class="sub-menu" aria-expanded="false">
                 <li><a href="<?= site_url('api/dutopup'); ?>" title="Du Topup"  data-bs-toggle="tooltip" data-bs-placement="right">Du Topup</a></li>
                <li><a href="<?= site_url('api/noltopup'); ?>" title="Nol Topup"  data-bs-toggle="tooltip" data-bs-placement="right">Nol Topup</a></li>
                <li><a href="<?= site_url('api/nationalbond'); ?>" title="National Bond"  data-bs-toggle="tooltip" data-bs-placement="right">National Bond</a></li>
                

                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Transportation</a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="<?= site_url('api/salikdirect'); ?>" title="Salik Direct"  data-bs-toggle="tooltip" data-bs-placement="right">Salik Direct</a></li>
               
            </ul>
        </li>
        <!-- <li>
            <a href="#" class="has-arrow">International Topup</a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="<?= site_url('api/internationrecharge'); ?>" title="International Recharge"  data-bs-toggle="tooltip" data-bs-placement="right">International Recharge</a></li>
                
                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Telecomunication</a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="<?= site_url('api/dupostpaid'); ?>" title="Du Postpaid"  data-bs-toggle="tooltip" data-bs-placement="right">Du Postpaid</a></li>
                <li><a href="<?= site_url('api/dutopup'); ?>" title="Du prepaid"  data-bs-toggle="tooltip" data-bs-placement="right">Du prepaid</a></li>
                <li><a href="<?= site_url('api/salikdirect'); ?>" title="Salik direct"  data-bs-toggle="tooltip" data-bs-placement="right">Salik direct</a></li>
                
             
                
            </ul>
        </li>
        <li>
            <a href="#" class="has-arrow">Other</a>
            <ul class="sub-menu" aria-expanded="false">
                 
                <li><a href="<?= site_url('api/dubai'); ?>" title="Dubai Police"  data-bs-toggle="tooltip" data-bs-placement="right">Dubai Police</a></li>
                <li><a href="<?= site_url('api/etisalat'); ?>" title="Etisalat"  data-bs-toggle="tooltip" data-bs-placement="right">Etisalat</a></li>
                <li><a href="<?= site_url('api/fewa'); ?>" title="Fewa"  data-bs-toggle="tooltip" data-bs-placement="right">Fewa</a></li>
                <li><a href="<?= site_url('api/hafilat'); ?>" title="Hafilat"  data-bs-toggle="tooltip" data-bs-placement="right">Hafilat</a></li>
                <li><a href="<?= site_url('api/mawaqif'); ?>" title="MAWAQIF PVT"  data-bs-toggle="tooltip" data-bs-placement="right">MAWAQIF PVT</a></li>
                <li><a href="<?= site_url('api/onlinecharaty'); ?>" title="Online charity"  data-bs-toggle="tooltip" data-bs-placement="right">Online charity</a></li>
                <li><a href="<?= site_url('api/sergas'); ?>" title="Sergas"  data-bs-toggle="tooltip" data-bs-placement="right">Sergas</a></li>
                <li><a href="<?= site_url('api/upay'); ?>" title="Upay"  data-bs-toggle="tooltip" data-bs-placement="right">Upay</a></li>
                <li><a href="<?= site_url('api/ajmandirect'); ?>" title="Ajman direct"  data-bs-toggle="tooltip" data-bs-placement="right">Ajman direct</a></li>
                <li><a href="<?= site_url('api/dubaided'); ?>" title="Dubai ded"  data-bs-toggle="tooltip" data-bs-placement="right">Dubai ded</a></li>
                <li><a href="<?= site_url('api/lootahgas'); ?>" title="Lootah gas"  data-bs-toggle="tooltip" data-bs-placement="right">Lootah gas</a></li>
                <li><a href="<?= site_url('api/offlinecharity'); ?>" title="Offline Charity"  data-bs-toggle="tooltip" data-bs-placement="right">Offline Charity</a></li>
               
                
            </ul>
        </li>  -->
    </ul>
</li> 
<?php endif; ?>
 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <!-- <li>
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
                    </li> -->
                <?php endif; ?>


 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                    <!-- <li>
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
                    </li> -->
                <?php endif; ?>

               
 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','Merchant','merchant','MERCHANT'])): ?>
                   <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                           <img src="<?= base_url('assets/images/employees.png'); ?>" 
     alt="Reports"
     style="width: 19px; height: 16px; margin-right: 8px; filter: invert(38%) sepia(10%) saturate(600%) hue-rotate(180deg) brightness(90%) contrast(90%);">

                            <span key="t-reportsMenu">EMS</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="<?= site_url('employees'); ?>" class="waves-effect">
                                    <i class="bx bx-id-card"></i>
                                    <span key="bx bx-id-card">Employees</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('employee-locations'); ?>" class="waves-effect">
                                    <i class="bx bx-map-pin"></i>
                                    <span key="bx bx-map-pin">Track Location</span>
                                </a>
                            </li>
                            
                             <li>
                                <a href="<?= site_url('employee-tasks'); ?>" class="waves-effect">
                                    <i class="bx bx-task"></i>
                                    <span key="bx bx-task">Asign Task</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('salary'); ?>" class="waves-effect">
                                    <i class="bx bx-money"></i>
                                    <span key="bx bx-money">Salaries info</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('request-letters'); ?>" class="waves-effect">
                                    <i class="bx bx-envelope"></i>
                                    <span key="t-rent-product">Request Letter</span>
                                </a>
                            </li>
                        </ul>
                    </li> 
                <?php endif; ?>

                <?php if (in_array('Subscription', $moduleAccess)): ?>
                    <!-- <li>
                        <a href="#" class="waves-effect">
                            <i class="bx bx-shopping-bag"></i>
                            <span key="t-purchases">Subscription</span>
                        </a>
                    </li> -->
                <?php endif; ?>

                              

<?php if (in_array(strtolower(session()->get('user_type')), ['superadmin','merchant'])): ?>
    <li class="has-submenu">
        <a href="javascript:void(0);" class="waves-effect">
            <img src="<?= base_url('assets/images/employees.png'); ?>" 
                 alt="Norka Roots"
                 style="width: 19px; height: 16px; margin-right: 8px; filter: invert(38%) sepia(10%) saturate(600%) hue-rotate(180deg) brightness(90%) contrast(90%);">
            <span key="t-rentMenu">Norka Roots</span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="submenu">
            <li><a href="<?= site_url('admin/norka/insurance'); ?>">Norka Insurance</a></li>
            <li><a href="<?= site_url('admin/norka/care'); ?>">Norka Care</a></li>
        </ul>
    </li>
<?php endif; ?>
<?php if (in_array(strtolower(session()->get('user_type')), ['superadmin','merchant'])): ?>
    <?php 
        $aiLink = session()->get('aiRedirectUrl'); 
    ?>
    <li>
        <a href="<?= !empty($aiLink) && $aiLink !== '#' ? esc($aiLink) : 'https://main.d2v3qt7xx431yq.amplifyapp.com/login' ?>" 
           id="aiAgentLink"
           target="_self" 
           class="waves-effect">
            <img src="<?= base_url('assets/images/Ai-Agent.png'); ?>" 
                 alt="Reports"
                 style="width: 19px; height: 16px; margin-right: 8px;
                        filter: invert(38%) sepia(10%) saturate(600%) 
                                hue-rotate(180deg) brightness(90%) contrast(90%);">
            <span key="t-rentMenu">AI Agent</span>
        </a>
    </li>

    <script>
        (function() {
            let aiLink = "<?= esc($aiLink) ?>";
            let linkEl = document.getElementById("aiAgentLink");

            // Agar 2 min ke andar aiLink update ho jata hai, to wahi use hoga
            if (!aiLink || aiLink === "#") {
                setTimeout(function() {
                    // 2 min (120 sec) ke baad agar link abhi bhi empty hai to fallback
                    if (!aiLink || aiLink === "#") {
                        linkEl.setAttribute("href", "https://main.d2v3qt7xx431yq.amplifyapp.com/login");
                    }
                }, 120000); // 120,000 ms = 2 minutes
            }
        })();
    </script>
<?php endif; ?>



<!-- <?php if (in_array(strtolower(session()->get('user_type')), ['superadmin','merchant'])): ?>
    <li>
        <a href="<?= esc($aiRedirectUrl ?? '#') ?>" target="_blank" class="waves-effect">
            <i class="bx bx-file"></i>
            <span key="t-reportsMenu">AI Reminders</span>
        </a>
    </li>
<?php endif; ?> -->
             
<!-- Payments -->

<?php if (in_array(strtolower(session()->get('user_type')), ['superadmin','merchant'])): ?>
                    
                    
                   

                    <li>
        <a href="<?= site_url('admin/transaction-report'); ?>"  class="waves-effect">
             
             <img src="<?= base_url('assets/images/reports.png'); ?>" 
     alt="Reports"
     style="width: 19px; height: 16px; margin-right: 8px; filter: invert(38%) sepia(10%) saturate(600%) hue-rotate(180deg) brightness(90%) contrast(90%);">

            <span key="t-rentMenu">Reports</span>
        </a>
    </li>
                <?php endif; ?>

              

                

                 <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin'])): ?>
                    
                    
                   

                    <!-- <li>
        <a href="<?= site_url('admin/settings'); ?>"  class="waves-effect">
             
             <img src="<?= base_url('assets/images/reports.png'); ?>" 
     alt="Reports"
     style="width: 19px; height: 16px; margin-right: 8px; filter: invert(38%) sepia(10%) saturate(600%) hue-rotate(180deg) brightness(90%) contrast(90%);">

            <span key="t-rentMenu">settings</span>
        </a>
    </li> -->
                <?php endif; ?>
              
            </ul>
          <!-- Sidebar Footer (add this after </ul>) -->
<div class="sidebar-footer text-center p-3" 
     style="position: fixed; bottom: 0; left: 0; width: 250px; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            background: #0d1b2a; z-index: 100;">
    <div style="font-size: 16px; font-weight: 600; color:#fff;">
        Trust Point
    </div>
    <div style="font-size: 12px; color:#bbb;">
        Powered by : RoyalXPay
    </div>
</div>

        </div>
        <!-- Sidebar -->
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function(){
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
<!-- Left Sidebar End --> lease regiter invoice summ