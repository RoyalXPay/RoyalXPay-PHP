<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="<?= site_url('user/dashboard'); ?>">
                        <span class="badge rounded-pill bg-primary float-end" key="t-hot">2</span>
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title" key="t-apps">Apps</li>
                <li>
                    <a href="<?= site_url('user/orders'); ?>" class="waves-effect">
                        <i class="bx bx-package"></i>
                        <span key="t-orders">My Orders</span>
                    </a>
                </li>
            </ul>
        </div><!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->