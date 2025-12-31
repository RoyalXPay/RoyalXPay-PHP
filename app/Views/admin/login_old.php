<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | Sign In - Access Your Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png'); ?>">
    <link href="https://cdn.materialdesignicons.com/5.9.55/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- Bootstrap Css -->
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css" />
    <!-- App js -->
    <script src="<?= base_url('assets/js/plugin.js'); ?>"></script>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary-subtle">
                            <div class="row">
                                <div class="col-7">
                                    <div class="p-4" style="color: #541D6C;">
                                        <h5 style="color: #541D6C;">Welcome Back !</h5>
                                        <p>Sign in to continue to Royal XPay.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="<?= base_url('assets/images/profile-img.png'); ?>" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="text-center mt-2 p-2" style=" width: 200px; margin: auto;">
                                <a href="<?php echo site_url("admin"); ?>">
                                    <img src="<?php echo base_url('assets/images/logo.png'); ?>" height="50" alt="logo" class="img-fluid">
                                </a>
                            </div>

                            <div class="p-2">
                                <form class="form-horizontal" method="post" action="<?= site_url('verify-login') ?>">
                                    <?= \Config\Services::validation()->listErrors(); ?>
                                    <?= csrf_field() ?>
                                    <?= view('flash_messages'); ?>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Email</label>
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter email address">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup" id="password-group">
                                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                            <button class="btn btn-light" type="button" id="password-addon" onclick="showHidePassword('password-group')">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" name="remember" type="checkbox" id="remember-check">
                                        <label class="form-check-label" for="remember-check">Remember me</label>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                    </div>

                                    <div class="mt-4 mb-4 text-center">
                                        <a href="#" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-muted">Don't have an account? 
                                            <a href="<?= site_url('register'); ?>" class="text-primary fw-bold">Register Now</a>
                                        </p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end account-pages -->
    <script>
        function showHidePassword() {
            var passwordField = $('#password');
            var icon = $('#password-addon i');

            if (passwordField.attr("type") === "password") {
                passwordField.attr('type', 'text');
                icon.removeClass("mdi-eye-outline").addClass("mdi-eye");
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass("mdi-eye").addClass("mdi-eye-outline");
            }
        }
    </script>
    <!-- JAVASCRIPT -->
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/node-waves/waves.min.js'); ?>"></script>

</body>

</html>