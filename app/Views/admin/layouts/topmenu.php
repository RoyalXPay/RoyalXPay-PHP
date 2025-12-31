
<style>
.dark-mode {
    background-color: #1e1e2f;
    color: #ffffff;
}
.dark-mode .card, 
.dark-mode .table {
    background-color: #2a2a40;
    color: #ffffff;
}


</style>
<?php 

        $session = session();


$userType           = strtolower(session()->get('user_type') ?? '');
$moduleAccess       = array_map('strtolower', (array)(session()->get('module_access') ?? []));
$subModuleAccess    = array_map('strtolower', (array)(session()->get('submodule_access') ?? []));
$subSubModuleAccess = array_map('strtolower', (array)(session()->get('subsubmodule_access') ?? []));


?>
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
           

            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?php echo site_url('admin/dashboard'); ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?= base_url('assets/images/logo.svg'); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= base_url('assets/images/dashboard-logo.png'); ?>" alt="" height="17">
                    </span>
                </a>

                <a href="<?php echo site_url('admin/dashboard'); ?>" class="logo logo-light mb-0">
                    <span class="logo-sm">
                        <img src="<?= base_url('assets/images/logo-light.svg'); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= base_url('assets/images/dashboard-logo.png'); ?>" style="margin-top: 10px; width:194px;" alt="" height="70">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

        </div>

        <!-- Centered Date and Time Display -->
        <div style="text-align: center;">
            <div id="day-yearDisplay" class="text-dark font-size-18"></div>
            <div id="timeDisplay" class="text-dark font-size-18"></div>
            <div class="text-dark font-size-16 fw-bold mt-1">
       
        
    </div>
        </div>

      


        <div class="d-flex">

                <!-- Wallet Display -->
            <?php
            use App\Models\UsersModel;

            $walletAmount = 0;
            $userId = session()->get('user_id');
            $user_type = session()->get('user_type');
            if ($userId) {
                $userModel = new \App\Models\UsersModel();
                $user = $userModel->find($userId);
                if ($user && isset($user['wallet'])) {
                    $walletAmount = (float) $user['wallet'] ?? 0; 
                     $mbmebalance = '30009';// cast to float to be safe
                }
            }
            ?>
            <?php if(in_array('wallet', $moduleAccess) || $user_type == 'superadmin') { ?>
            <div class="dropdown d-inline-block align-self-center mx-3" style="    text-align: right;color:#fff;">
                <span class="badge bg-info text-dark font-size-14 p-2">
                    <a href="<?= site_url('manage-wallet/recharge') ?>" style="    text-align: right;color:#fff;    font-size: 14px !important;" >Wallet Recharge</a>

            </span>

            </div>
            <?php } ?>

              <?php if( $user_type == 'superadmin' || $user_type == 'superadmin') { ?>
            <div class="dropdown d-inline-block align-self-center mx-3" style="    text-align: right;color:#fff;">
                <span class="badge bg-info text-dark font-size-14 p-2">
                    <a href="#" style="text-align: right;color:#fff;    font-size: 14px !important;" >MBME Balance: <?= number_format($mbmebalance, 2); ?></a>

            </span>

            </div>
            <?php } ?>
<?php if(in_array('wallet', $moduleAccess) || $user_type == 'superadmin' || $user_type == 'superadmin') { ?>
            <div class="dropdown d-inline-block align-self-center mx-3" style="    text-align: right;color:#fff;">
                 <span class="badge bg-info text-dark font-size-14 p-2">
                    <a href="<?= site_url('wallet-history/' . $userId) ?>"  style="    text-align: right;color:#fff;    font-size: 14px !important;">
                    Wallet: AED <?= number_format($walletAmount, 2); ?>
                </a>
                </span>
            </div>
 <?php } ?>
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications Icon -->
<div class="dropdown d-inline-block mx-2" id="notificationWrapper">
    <button type="button" class="btn header-item noti-icon waves-effect" id="notifIcon" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-bell"></i>
        <span class="badge bg-danger rounded-pill" id="notifCount">0</span>
    </button>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
        <div class="p-3">
            <h6 class="m-0">Recharge Notifications</h6>
        </div>
        <div style="max-height: 250px; overflow-y: auto;" id="notifList">
            <p class="dropdown-item">Loading...</p>
        </div>
    </div>
</div>
<?php
$userId = session()->get('user_id');
$userModel = new \App\Models\UsersModel();
$user = $userModel->find($userId);

$logoUrl = base_url('uploads/logos/default-profile.jpeg'); // default logo
if ($user && !empty($user['logo'])) {
    $logoUrl = base_url($user['logo']);
}
?>
            <div class="dropdown d-inline-block">
                
               <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <img src="<?= $logoUrl ?>" alt="Company Logo" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">
</button>

              <div class="dropdown-menu dropdown-menu-end">
    <!-- Company Details -->
    <a class="dropdown-item" href="<?= site_url('admin/profile'); ?>">
        <i class="bx bx-building font-size-16 align-middle me-1"></i> 
        <span key="t-company">Company Details</span>
    </a>

    <!-- Profile -->
    <!-- <a class="dropdown-item" href="<?= site_url('admin/profile'); ?>">
        <i class="bx bx-user font-size-16 align-middle me-1"></i> 
        <span key="t-profile">Profile</span>
    </a> -->

    <!-- Change Password -->

 <!-- Change Layout Toggle -->
   <!-- Layout Dropdown -->
<!-- Change Layout Dropdown -->
<!-- Change Layout Section -->
<div class="dropdown-item">
    <a class="d-flex align-items-center justify-content-between text-dark" href="#" id="layoutDropdownToggle">
        <div>
            <i class="bx bx-layout font-size-16 align-middle me-1"></i>
            <span>Change Layout</span>
        </div>
        <i class="bx bx-chevron-down" id="layoutArrow" style="font-size: 18px;"></i>
    </a>

    <!-- Hidden options (toggleable) -->
    <div id="layoutOptions" class="mt-2 ps-4" style="display: none;">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="darkModeCheck">
            <label class="form-check-label" for="darkModeCheck">Dark Mode</label>
        </div>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="lightModeCheck">
            <label class="form-check-label" for="lightModeCheck">Light Mode</label>
        </div>
    </div>
</div>



    <div class="dropdown-divider"></div>

    <!-- Logout -->
    <a class="dropdown-item text-danger" href="<?= site_url('admin/logout'); ?>">
        <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> 
        <span key="t-logout">Logout</span>
    </a>
</div>


            <!-- <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div> -->

        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const layoutToggle = document.getElementById('layoutDropdownToggle');
    const layoutOptions = document.getElementById('layoutOptions');
    const layoutArrow = document.getElementById('layoutArrow');
    const darkCheck = document.getElementById('darkModeCheck');
    const lightCheck = document.getElementById('lightModeCheck');

    // Prevent dropdown from closing when clicking inside layout section
    const layoutDropdown = layoutToggle.closest('.dropdown-item');
    layoutDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Toggle show/hide of layout options
    layoutToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // important to prevent popup close
        layoutOptions.style.display = layoutOptions.style.display === 'none' ? 'block' : 'none';
        layoutArrow.classList.toggle('bx-rotate-180');
    });

    // Apply saved mode
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        darkCheck.checked = true;
        lightCheck.checked = false;
    } else {
        document.body.classList.remove('dark-mode');
        lightCheck.checked = true;
        darkCheck.checked = false;
    }

    // Handle dark mode toggle
    darkCheck.addEventListener('change', function(e) {
        e.stopPropagation();
        if (darkCheck.checked) {
            document.body.classList.add('dark-mode');
            lightCheck.checked = false;
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            lightCheck.checked = true;
            localStorage.setItem('theme', 'light');
        }
    });

    // Handle light mode toggle
    lightCheck.addEventListener('change', function(e) {
        e.stopPropagation();
        if (lightCheck.checked) {
            document.body.classList.remove('dark-mode');
            darkCheck.checked = false;
            localStorage.setItem('theme', 'light');
        } else {
            document.body.classList.add('dark-mode');
            darkCheck.checked = true;
            localStorage.setItem('theme', 'dark');
        }
    });
});
</script>


<script>
    
document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications();

   function fetchNotifications() {
    fetch("<?= site_url('notifications') ?>")
        .then(response => response.json())
        .then(data => {
            document.getElementById("notifCount").innerText = data.count;

            const notifList = document.getElementById("notifList");
            notifList.innerHTML = "";

            if (data.notifications.length === 0) {
                notifList.innerHTML = '<p class="dropdown-item">No new notifications</p>';
                return;
            }

            data.notifications.forEach(n => {
                const item = document.createElement('div');
                item.className = "dropdown-item d-flex justify-content-between align-items-start";

                // left content
                const content = document.createElement('div');
                content.className = "flex-grow-1";
                content.innerHTML = `
                    <h6 class="mt-0 mb-1">Recharge request of AED ${n.amount}</h6>
                    <div class="font-size-12 text-muted">
        Merchant: ${n.merchant_name ?? 'N/A'}<br>
        Status: <span class="${n.status === 'pending' ? 'text-warning' : 'text-success'}">${n.status}</span>
    </div>
                `;

                // add click to mark-done and redirect for normal notifications
                if (n.wallet_by !== 'Merchant Recharge') {
                    item.style.cursor = "pointer";
                    item.addEventListener("click", function () {
                        fetch("<?= site_url('notifications/mark-done/') ?>" + n.id, {
                            method: "POST",
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(response => {
                            if (response.success) {
                                window.location.href = "<?= site_url('wallet-history/') ?>" + (n.user_id ?? '');
                            } else {
                                alert('Failed to update notification.');
                            }
                        });
                    });
                }

                item.appendChild(content);

                // If Merchant Recharge, show Receive button
                if (n.wallet_by === 'Merchant Recharge') {
                    const button = document.createElement('button');
                    button.className = "btn btn-sm btn-success ms-2";
                    button.textContent = "Receive";

                    button.onclick = function (e) {
                        e.stopPropagation(); // prevent parent click
                        if (!confirm("Are you sure to mark this recharge as received?")) return;
                        fetch("<?= site_url('wallet/approve-recharge/') ?>" + n.id, {
                            method: "POST",
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.json())
                        .then(response => {
                            if (response.status === 'success') {
                                alert("Recharge approved and credited.");
                                location.reload();
                            } else {
                                alert("Failed to approve: " + response.message);
                            }
                        });
                    };

                    item.appendChild(button);
                }

                notifList.appendChild(item);
            });
        });
}

});



</script>



