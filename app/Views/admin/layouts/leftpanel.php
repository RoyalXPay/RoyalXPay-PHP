<!-- ========== Left Sidebar Start ========== -->
<style>
    #toolbarContainer{
    display: none;
}

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
    /* Keep text white on hover */
    body[data-sidebar=dark] #sidebar-menu ul li a:hover {
        color: #fff;
    }
 /* Add space so last items do not hide behind footer */
    .vertical-menu .h-100 {
        padding-bottom: 90px; /* adjust height */
    }

    .sidebar-footer {
        height: 70px; /* fixed height */
    }
</style>

<?php 

        $session = session();


$userType           = strtolower(session()->get('user_type') ?? '');
$moduleAccess       = array_map('strtolower', (array)(session()->get('module_access') ?? []));
$subModuleAccess    = array_map('strtolower', (array)(session()->get('submodule_access') ?? []));
$subSubModuleAccess = array_map('strtolower', (array)(session()->get('subsubmodule_access') ?? []));


?>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                
                <!-- Dashboard -->
                <li>
                    <a href="<?= site_url('admin/dashboard'); ?>">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Dashboard</span>
                    </a>
                </li>

                <!-- Customers -->
                <?php if (in_array('customers', $moduleAccess) || in_array($userType, ['superadmin'])): ?>
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user"></i>
                        <span>Customers</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (in_array('customers', $subModuleAccess) || in_array($userType, ['superadmin'])): ?>
                            <li><a href="<?= site_url('customers'); ?>">All Customers</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Merchant -->
                <?php if ($userType === 'superadmin' || in_array('merchant', $moduleAccess)): ?>
                <li>
                    <a href="<?= site_url('merchant'); ?>" class="waves-effect">
                        <i class="bx bx-file"></i><span>Merchant</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Manage Wallet -->
               <?php if (in_array($userType, ['superadmin'])): ?>
                <li>
                    <a href="<?= site_url('manage-wallet'); ?>" class="waves-effect">
                        <i class="bx bx-wallet"></i><span>Manage Wallet</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Wallet History (merchant only) -->
                 <?php if (in_array('wallet', $moduleAccess) || in_array($userType, ['superadmin'])): 
                    $userId = session()->get('user_id'); ?>
                <li>
                    <a href="<?= site_url('wallet-history/' . $userId); ?>" class="waves-effect">
                        <i class="fas fa-history"></i> Wallet History
                    </a>
                </li>
                <?php endif; ?>

                <!-- Recharge & Bill Payments -->
              <?php if (
    (in_array('recharge & bill payments', $moduleAccess) || in_array($userType, ['superadmin'])) &&
    !in_array($userType, ['checker', 'maker'])
): ?>
                <li>
                    <a href="#" class="has-arrow waves-effect">
                        <i class="bx bx-credit-card"></i><span>Recharge & Bill Payments</span>
                    </a>
                    <ul class="sub-menu">

                        <!-- Utilities Payment -->
                        <?php if (in_array('utilities payment', $subModuleAccess) || in_array($userType, ['superadmin'])): ?>
                        <li>
                            <a href="#" class="has-arrow">Utilities Payment</a>
                            <ul class="sub-menu">
                                <?php if (in_array('aadc', $subSubModuleAccess) || in_array($userType, ['superadmin'])): ?>
                                    <li><a href="<?= site_url('api/aadc'); ?>">AADC</a></li>
                                <?php endif; ?>
                                <?php if (in_array('addc', $subSubModuleAccess) || in_array($userType, ['superadmin'])): ?>
                                    <li><a href="<?= site_url('api/addc'); ?>">ADDC</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Topup -->
                        <?php if (in_array('topup', $subModuleAccess) || in_array($userType, ['superadmin'])): ?>
                        <li>
                            <a href="#" class="has-arrow">Topup</a>
                            <ul class="sub-menu">
                                <?php if (in_array('du topup', $subSubModuleAccess) || in_array($userType, ['superadmin'])): ?>
                                    <li><a href="<?= site_url('api/dutopup'); ?>">Du Topup</a></li>
                                <?php endif; ?>
                                <?php if (in_array('nol topup', $subSubModuleAccess) || in_array($userType, ['superadmin'])): ?>
                                    <li><a href="<?= site_url('api/noltopup'); ?>">Nol Topup</a></li>
                                <?php endif; ?>
                                <?php if (in_array('national bond', $subSubModuleAccess) || in_array($userType, ['superadmin'])): ?>
                                    <li><a href="<?= site_url('api/nationalbond'); ?>">National Bond</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Transportation -->
                        <?php if (in_array('transportation', $subModuleAccess) || in_array($userType, ['superadmin'])): ?>
                        <li>
                            <a href="#" class="has-arrow">Transportation</a>
                            <ul class="sub-menu">
                                <?php if (in_array('salik direct', $subSubModuleAccess) || in_array($userType, ['superadmin'])): ?>
                                    <li><a href="<?= site_url('api/salikdirect'); ?>">Salik Direct</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Other -->
                        <?php if (in_array('others', $subModuleAccess) || in_array($userType, ['superadmin','Merchant'])): ?>
                        <li>
                            <a href="#" class="has-arrow">Other</a>
                            <ul class="sub-menu">
                                <?php if (in_array('sergas', $subSubModuleAccess) || in_array($userType, ['superadmin','Merchant'])): ?>
                                    <li><a href="<?= site_url('api/sergas'); ?>">Sergas</a></li>
                                <?php endif; ?>
                                <?php if (in_array('ajman direct', $subSubModuleAccess) || in_array($userType, ['superadmin','Merchant'])): ?>
                                    <li><a href="<?= site_url('api/ajmandirect'); ?>">Ajman Direct</a></li>
                                <?php endif; ?>
                                  <?php if (in_array('lootah gas', $subSubModuleAccess) || in_array($userType, ['superadmin','Merchant'])): ?>
                                    <li><a href="<?= site_url('api/lootahgas'); ?>">Lootah Gas</a></li>
                                <?php endif; ?>
                                
                              
                            </ul>
                        </li>
                        <?php endif; ?>

                    </ul>
                </li>
                <?php endif; ?>

                <!-- EMS -->
              
                <?php if (in_array($userType, ['superadmin','checker','maker','Merchant','merchant'])): ?>
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <img src="<?= base_url('assets/images/employees.png'); ?>" style="width:19px;height:16px;margin-right:8px;">
                        <span>EMS</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ( in_array($userType, ['superadmin','Merchant','merchant'])): ?>
                            <li><a href="<?= site_url('employees'); ?>"><i class="bx bx-id-card"></i> Employees</a></li>
                        <?php endif; ?>
                        <?php if ( in_array($userType, ['superadmin','Merchant','merchant'])): ?>
                            <li><a href="<?= site_url('employee-locations'); ?>"><i class="bx bx-map-pin"></i> Track Location</a></li>
                        <?php endif; ?>
                        <?php if ( in_array($userType, ['superadmin','Merchant','merchant'])): ?>
                            <li><a href="<?= site_url('employee-tasks'); ?>"><i class="bx bx-task"></i> Assign Task</a></li>
                        <?php endif; ?>
                        <?php if ( in_array($userType, ['superadmin','Merchant','merchant'])): ?>
                            <li><a href="<?= site_url('salary'); ?>"><i class="bx bx-money"></i> Salaries info</a></li>
                        <?php endif; ?>
                        <?php if (in_array($userType, ['superadmin','Merchant','merchant'])): ?>
                            <li><a href="<?= site_url('request-letters'); ?>"><i class="bx bx-envelope"></i> Request Letters</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

               


                <!-- Norka Roots -->
                <?php if (in_array('norka root', $moduleAccess) || in_array($userType, ['superadmin'])): ?>
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <img src="<?= base_url('assets/images/employees.png'); ?>" style="width:19px;height:16px;margin-right:8px;">
                        <span>Norka Roots</span>
                    </a>
                    <ul class="sub-menu">
                      
                            <li><a href="<?= site_url('admin/norka/insurance'); ?>">Norka Insurance</a></li>
                        
                       
                            <li><a href="<?= site_url('admin/norka/care'); ?>">Norka Care</a></li>
                       
                    </ul>
                </li>
                <?php endif; ?>

                <!-- AI Agent -->
                <?php if (in_array('ai agent', $moduleAccess) || in_array($userType, ['superadmin'])): 
                    $aiLink = session()->get('aiRedirectUrl'); ?>
                <li>
                    <a href="<?= !empty($aiLink) && $aiLink !== '#' ? esc($aiLink) : 'https://main.d2v3qt7xx431yq.amplifyapp.com/login' ?>" id="aiAgentLink" target="_self" class="waves-effect">
                        <img src="<?= base_url('assets/images/Ai-Agent.png'); ?>" style="width:19px;height:16px;margin-right:8px;">
                        <span>AI Agent</span>
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

                <!-- Reports -->
                <?php if (in_array('reports', $moduleAccess) || in_array($userType, ['superadmin'])): ?>
                <li>
                    <a href="<?= site_url('admin/transaction-report'); ?>" class="waves-effect">
                        <img src="<?= base_url('assets/images/reports.png'); ?>" style="width:19px;height:16px;margin-right:8px;">
                        <span>Reports</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Payments Menu -->
                        
                <!-- Settings -->
                <?php if ( in_array($userType, ['superadmin'])): ?>
                <li>
                    <a href="#" class="waves-effect has-arrow">
                        <img src="<?= base_url('assets/images/reports.png'); ?>" style="width:19px;height:16px;margin-right:8px;">
                        <span>Settings</span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ( in_array($userType, ['superadmin'])): ?>
                            <li><a href="<?= site_url('admin/privileges'); ?>">Privileges</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer text-center p-3" style="position: fixed; bottom: 0; left: 0; width: 250px; border-top: 1px solid rgba(255,255,255,0.1); background: #0d1b2a; z-index: 100;">
                <div style="font-size:16px;font-weight:600;color:#fff;">Trust Point</div>
                <div style="font-size:12px;color:#bbb;">Powered by : RoyalXPay</div>
            </div>
        </div>
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