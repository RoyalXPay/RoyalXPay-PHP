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

            if ($userId) {
                $userModel = new \App\Models\UsersModel();
                $user = $userModel->find($userId);
                if ($user && isset($user['wallet'])) {
                    $walletAmount = (float) $user['wallet'] ?? 0; // cast to float to be safe
                }
            }
            ?>
            <div class="dropdown d-inline-block align-self-center mx-3" style="    text-align: right;">
                <span class="badge bg-info text-dark font-size-14 p-2">
                    Wallet: AED <?= $walletAmount; ?>
                </span>
            </div>
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

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-user bx-sm fs-2 rounded-circle header-profile-user"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="<?= site_url('admin/profile'); ?>"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                    <a class="dropdown-item d-block" href="<?= site_url('admin/change-password'); ?>"><i class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings">Change Password</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?= site_url('admin/logout'); ?>"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div>

        </div>
    </div>
</header>