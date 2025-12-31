   <?php
            use App\Models\UsersModel;
            ?><style>
    .filter-btn.active {
           color: var(--bs-btn-active-color);
    background-color: var(--bs-btn-active-bg);
    border-color: var(--bs-btn-active-border-color);
    box-shadow: rgba(85, 110, 230, 0.384) 0px 0px 0px 1.83927px !important;        
    }
     .filter-btn.focus {
           color: var(--bs-btn-active-color);
    background-color: var(--bs-btn-active-bg);
    border-color: var(--bs-btn-active-border-color);
        
    }
</style>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<pre>
<?php
// print_r($employeeCount);
// print_r($todayEmployeeCount);
// print_r(strtolower(session()->get('user_type')));die();
?>
</pre>

<?php 

        $session = session();


$userType           = strtolower(session()->get('user_type') ?? '');
$moduleAccess       = array_map('strtolower', (array)(session()->get('module_access') ?? []));
$subModuleAccess    = array_map('strtolower', (array)(session()->get('submodule_access') ?? []));
$subSubModuleAccess = array_map('strtolower', (array)(session()->get('subsubmodule_access') ?? []));


?>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <?php
            $session = session();
            $user_type = strtolower($session->get('user_type'));
            $name = $session->get('name'); // Make sure you store this in session during login

            $user_type_display = ucfirst($user_type);
            if (in_array($user_type, ['merchant', 'employee', 'user'])) {
                 echo "Welcome " . ucfirst($name) . " ,";
            } elseif ($user_type === 'superadmin') {
                echo "Welcome SuperAdmin : Dashboard";
            } else {
                echo "Welcome {$user_type_display} : Dashboard";
            }
        ?></h4>
                     
<?php if (in_array(strtolower(session()->get('user_type')), [ 'admin','superadmin'])): ?>
                    <div class="col">
                                    <center>
                                        <button class="btn btn-outline-primary filter-btn" data-filter="b2b">B2B</button>
                                        <button class="btn btn-outline-primary filter-btn" data-filter="b2b2c">B2B2C</button>
                                        <button class="btn btn-outline-primary filter-btn" data-filter="d2c">D2C</button>
                                        <button class="btn btn-outline-primary filter-btn" data-filter="pos">POS</button>
                                        <button class="btn btn-outline-primary filter-btn" data-filter="all">ALL</button>
                                    </center>
                                    </div>
                                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
           <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','admin'])): ?>
            <!-- 4 Visible cards initially -->
            <div class="col-md-3 b2b2c-card">
                <a href="<?= site_url('customers') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Users</p>
                                    <h4 class="mb-0"><?= $customerCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-user font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            

            <div class="col-md-3 b2b2c-card">
                <a href="<?= site_url('customers') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Today’s Users</p>
                                    <h4 class="mb-0"><?= $customerCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-user font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

          
            <?php if (in_array(strtolower(session()->get('user_type')), [ 'superadmin','admin','maker','checker','merchant'])){ ?>
            <!-- 4 Visible cards initially -->
            <div class="col-md-3 b2b-card" style="display:block;">
                <a href="<?= site_url('employees') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Today’s Employee</p>
                                    <h4 class="mb-0"><?= $todayEmployeeCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-user font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>
            <div class="col-md-3 pos-card">
                <a href="<?= site_url('product-master') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Earning</p>
                                    <h4 class="mb-0"><?= $productCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-package font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 pos-card">
                <a href="<?= site_url('product-master') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Today’s Earning</p>
                                    <h4 class="mb-0"><?= $productCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-package font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 b2b-card">
                <a href="<?= site_url('merchant') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Marchant</p>
                                    <h4 class="mb-0"><?= $rentProductCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-package font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 b2b-card">
                <a href="<?= site_url('sale-products') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Today’s Marchant Earning</p>
                                    <h4 class="mb-0"><?= $saleProductCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-package font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 b2b-card">
                <a href="<?= site_url('rent-quotations') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Recharge and Bill Payment</p>
                                    <h4 class="mb-0"><?= $rentQuotationCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-file font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 b2b-card">
                <a href="<?= site_url('rent-contracts') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Today’s Recharge and Bill Payment</p>
                                    <h4 class="mb-0"><?= $rentContractCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-file-find font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 b2b2c-card">
                <a href="<?= site_url('sale-quotations') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Subscribed users</p>
                                    <h4 class="mb-0"><?= $saleQuotationCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-receipt font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 b2b2c-card">
                <a href="<?= site_url('sale-contracts') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Today's Subscribed users</p>
                                    <h4 class="mb-0"><?= $saleContractCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-file-find font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 d2c-card">
                <a href="<?= site_url('orders') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Transaction</p>
                                    <h4 class="mb-0"><?= $orderCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-task font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 d2c-card">
                <a href="<?= site_url('companies') ?>" class="card-link">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Today's Transaction</p>
                                    <h4 class="mb-0"><?= $companyCount ?></h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-building font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>
            <?php if (in_array(strtolower(session()->get('user_type')), ['merchant'])): ?>
    <!-- Wallet Balance -->
    <?php
        $walletAmount = 0;
        $userId = session()->get('user_id');

        if ($userId) {
            $userModel = new \App\Models\UsersModel();
            $user = $userModel->find($userId);
            if ($user && isset($user['wallet'])) {
                $walletAmount = (float) $user['wallet'] ?? 0; // cast to float to be safe
            }
        }
        ?>

  <?php if (in_array($userType, ['superadmin','merchant'])): ?>

<?php if (in_array($userType, ['superadmin','merchant'])) { ?>
<div class="col-md-3 all" style="display:block;">
    <a href="<?= site_url('employees') ?>" class="card-link">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Employees</p>
                        <h4 class="mb-0"><?= $employeeCount ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-user font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-md-3 all" style="display:block;">
    <a href="<?= site_url('task') ?>" class="card-link">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Assigned Tasks</p>
                        <h4 class="mb-0"><?= $taskCount ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i class="bx bx-task font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-md-3 all" style="display:block;">
    <a href="<?= site_url('request-letter') ?>" class="card-link">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Request</p>
                        <h4 class="mb-0"><?= $requestLetterCount ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i class="bx bx-envelope font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>
<?php } ?>

<?php endif; ?>

<?php if (in_array('manage wallet', $moduleAccess) || in_array($userType, ['superadmin'])): ?>
    <div class="col-md-3 all">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Wallet Balance</p>
                        <h4 class="mb-0"><?= number_format($walletAmount, 2) ?>  <small class="text-muted">AED</small></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-wallet font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +5.2% since last month</small>
                    <a href="<?= site_url('wallet-history/' . $userId) ?>" class="text-primary">
                        View Details <i class="bx bx-right-arrow-alt"></i>
                    </a>            
                </div>
        </div>
    </div>
 <?php endif; ?>

    <!-- Total Merchant Users -->
    <?php if (in_array('customers', $moduleAccess) || in_array($userType, ['superadmin'])): ?>
    <div class="col-md-3 all">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Customers</p>
                        <h4 class="mb-0"><?= $totalMerchantUsers ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-user-check font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +3.8% since last month</small>
                <a href="<?= base_url('customers') ?>" class="text-primary">View Details <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </div>
    </div>
 <?php endif; ?>
    <!-- Today's Merchant Users -->
 <?php if (in_array('customers', $moduleAccess) || in_array($userType, ['superadmin'])): ?>
    <div class="col-md-3 all">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Today's Customers</p>
                        <h4 class="mb-0"><?= $todayMerchantUsers ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-user-plus font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +2.1% today</small>
                <a href="<?= base_url('customers') ?>" class="text-primary">View Details <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </div>
    </div>
 <?php endif; ?>

    <!-- Today's Earning -->
 <?php if (in_array($userType, ['superadmin'])): ?>
    <div class="col-md-3 all">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Today's Earning</p>
                        <h4 class="mb-0"><?= number_format($todayEarning, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-money font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +6.0% today</small>
                <a href="#" class="text-primary">View Details <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </div>
    </div>
 <?php endif; ?>
 <?php if (in_array($userType, ['superadmin'])): ?>
    <!-- Total Earning -->
    <div class="col-md-3 all">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Total Earning</p>
                        <h4 class="mb-0"><?= number_format($totalEarning, 2) ?></h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-bar-chart font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                <small class="text-success"><i class="bx bx-up-arrow-alt"></i> +8.4% since last month</small>
                <a href="#" class="text-primary">View Details <i class="bx bx-right-arrow-alt"></i></a>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php endif; ?>

        </div>

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->



<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> © Royal XPay.
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    DBA Payment Services Provider.
                </div>
            </div>
        </div>
    </div>
</footer>
<script>
    // Function to filter cards
    function showCards(filter) {
        const allCards = document.querySelectorAll(
            '.b2b-card, .b2b2c-card, .d2c-card, .pos-card'
        );

        if (filter === 'all') {
            allCards.forEach(card => card.style.display = 'block');
        } else {
            allCards.forEach(card => {
                card.style.display = card.classList.contains(`${filter}-card`) ? 'block' : 'none';
            });
        }

        // Update button active state
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.getAttribute('data-filter') === filter) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // On DOM load
    document.addEventListener('DOMContentLoaded', function () {
        showCards('b2b'); // Show B2B cards and highlight B2B button

        // Attach click handlers
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const filter = this.getAttribute('data-filter');
                showCards(filter);
            });
        });
    });
</script>



<?= $this->endSection() ?>