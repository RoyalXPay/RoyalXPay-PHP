<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Register | Create Your Merchant Account - Royal XPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png'); ?>">
    <link href="https://cdn.materialdesignicons.com/5.9.55/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css" />
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
                                        <h5 style="color: #541D6C;">Join Us Today!</h5>
                                        <p>Create your merchant account to start using Royal XPay.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="<?= base_url('assets/images/profile-img.png'); ?>" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="text-center mt-2 p-2" style=" width: 200px; margin: auto;">
                                <a href="<?= site_url("admin"); ?>">
                                    <img src="<?= base_url('assets/images/logo.png'); ?>" height="50" alt="logo" class="img-fluid">
                                </a>
                            </div>

                            <div class="p-2">
                                <?php if (isset($validation)): ?>
                                    <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
                                <?php endif; ?>

                                <form method="post" action="<?= site_url('registerSave') ?>" class="form-horizontal">
                                    <?= csrf_field() ?>

                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" placeholder="Enter full name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone" placeholder="Enter mobile number" required>
                                    </div>

                                    <div class="mb-3">
    <label class="form-label">Country</label>
    <select name="country" class="form-control" required>
        <option value="">Select Country</option>
        <option value="India">India</option>
        <option value="UAE">UAE</option>
        <option value="Saudi Arabia">Saudi Arabia</option>
        <option value="Oman">Oman</option>
        <option value="Bangladesh">Bangladesh</option>
        <option value="Nepal">Nepal</option>
        <option value="Bhutan">Bhutan</option>
        <option value="Myanmar">Myanmar</option>
        <option value="Thailand">Thailand</option>
        <option value="Sri Lanka">Sri Lanka</option>
    </select>
</div>

                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-control">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="repeat_password" placeholder="Repeat password" required>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Register</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="<?= site_url('login') ?>" class="text-muted"><i class="mdi mdi-login me-1"></i> Back to Login</a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/node-waves/waves.min.js'); ?>"></script>
    <script>
      $('form').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '<?= site_url('registerSave') ?>',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.status === 'exists') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Registered',
                    text: response.message,
                });
            } else if (response.status === 'validation_error' || response.status === 'error') {
                let errorList = '';
                for (const field in response.errors) {
                    errorList += `<div>• ${response.errors[field]}</div>`;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorList
                });
            } else if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Registered Successfully',
                    text: 'Redirecting to login...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "<?= site_url('login') ?>";
                });
            }
        }
    });
});


    </script>
</body>

</html>
