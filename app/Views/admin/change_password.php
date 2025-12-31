<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- Start Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><i class="fas fa-lock"></i> Change Password</h4>
                        <p class="card-subtitle mb-2 text-muted">Fill in the details below to change your password.</p>

                        <form class="custom-validation" id="myForm" method="post" action="<?php echo site_url('update-password'); ?>" enctype="multipart/form-data">
                            <?= csrf_field(); ?>
                            <?php echo view('flash_messages'); ?>

                            <div class="form-mobile-laptop">

                                <!-- New Password Field -->
                                <div class="form-group row" style="margin-bottom: 10px;">
                                    <label for="password" class="col-sm-2 col-form-label">New Password</label>
                                    <div class="col-sm-6">
                                        <div class="input-group form-group" id="show_hide_password">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="New password" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <a href="javascript:void(0);" onclick="showHidePassword('show_hide_password')">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Password Field -->
                                <div class="form-group row" style="margin-bottom: 10px;">
                                    <label for="confirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
                                    <div class="col-sm-6">
                                        <div class="input-group form-group" id="confirm_show_hide_password">
                                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <a href="javascript:void(0);" onclick="showHidePassword('confirm_show_hide_password')">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                        <p id="passwordError" style="color:red;"></p>
                                    </div>
                                </div>

                                <!-- Submit and Back Buttons -->
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-6">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" id="owner-submit-btn">
                                            Submit
                                        </button>
                                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                                            <i class="ion ion ion-md-arrow-back"></i> Back
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div> <!-- container-fluid -->
</div><!-- End Page-content -->

<script>
    function showHidePassword(fieldId) {
        var field = $('#' + fieldId + ' input');
        var icon = $('#' + fieldId + ' i');

        if (field.attr("type") == "text") {
            field.attr('type', 'password');
            icon.removeClass("fa-eye");
            icon.addClass("fa-eye-slash");
        } else if (field.attr("type") == "password") {
            field.attr('type', 'text');
            icon.removeClass("fa-eye-slash");
            icon.addClass("fa-eye");
        }
    }

    var passwordField = document.getElementById("password");
    var confirmPasswordField = document.getElementById("confirmPassword");
    var passwordError = document.getElementById("passwordError");
    var form = document.getElementById("myForm");

    function checkPassword() {
        var password = passwordField.value;
        var confirmPassword = confirmPasswordField.value;

        if (password === confirmPassword) {
            passwordError.textContent = "";
        } else {
            passwordError.textContent = "Passwords do not match";
        }
    }

    passwordField.addEventListener("input", checkPassword);
    confirmPasswordField.addEventListener("input", checkPassword);

    form.addEventListener("submit", function(event) {
        if (passwordField.value !== confirmPasswordField.value) {
            event.preventDefault();
        }
    });
</script>
<?= $this->endSection() ?>