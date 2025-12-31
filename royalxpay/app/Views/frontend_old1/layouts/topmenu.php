    <!-- Navbar Area Started -->
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark site-header py-2" style=" background-color: #000000!important;">
            <div class="container">
            <a class="navbar-brand d-flex align-items-center ps-0" href="index.html">
               <img src="assets_frontend/images/logo.jpeg" alt="Royal pay" style="width: 250px; height: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mynavbar">
                <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link pe-3 active" href="./index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="./service.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="./company.php">Company</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="./contact-us.php">Contact Us</a>
                </li>
                </ul>
               <div class="d-flex align-items-center justify-content-center">
  <div id="auth-buttons">
<?php if (session()->has('customer_id')): ?>
    <p>Welcome, <?= session('customer_name') ?> |
        <a href="<?= base_url('customer-auth/logout') ?>">Logout</a>
    </p>
<?php else: ?>
    <button data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
    <a href="javascript:void(0);" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
<?php endif; ?>
</div>
            </div>
            </div>
        </nav>
    <!-- Navbar Area end -->
     