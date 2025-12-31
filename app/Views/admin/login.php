<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login | Royal XPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png'); ?>">
    <link href="https://cdn.materialdesignicons.com/5.9.55/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" rel="stylesheet" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    

    <style>
        #toolbarContainer{
    display: none;
}
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            overflow: hidden;
        }
        .full-height {
            height: 100vh;
        }

        .left-side img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .login-box {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            margin: auto;
        }

        .tab-btn {
            width: 50%;
            text-align: center;
            cursor: pointer;
            padding: 12px 0;
            font-weight: 600;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-bottom: none;
            color: #555;
            transition: background-color 0.3s;
        }

        .tab-btn.active {
            background-color: #fff;
            border-bottom: 2px solid #541D6C;
            color: #541D6C;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php if (isset($first_login_done) && $first_login_done == 1): ?>
   <style>#password-group { display:none; }</style>
<?php endif; ?>

<div class="container-fluid full-height">
    <div class="row full-height">
        <!-- Image Section -->
        <div class="col-md-7 d-none d-md-block p-0">
            <div class="left-side full-height">
                <img src="<?= base_url('assets/images/app-logo.png'); ?>" alt="Login Image">
            </div>
        </div>

        <!-- Login Form Section -->
        <div class="col-md-5 d-flex align-items-center justify-content-center bg-white">
            <div class="login-box">
                <div class="text-center mb-4">
                    <h2 class="mt-3" style="color:#541D6C; font-size: 2rem;">Login to RoyalXPay</h2>
                </div>

                <!-- Tabs -->
                <div class="d-flex mb-4 rounded overflow-hidden" style="border: 1px solid #ddd;">
                    <div class="tab-btn active" id="tab-email">Email</div>
                    <div class="tab-btn" id="tab-mobile">Mobile</div>
                </div>

               <!-- Email Login Form -->
            <form id="form-email" method="post" action="<?= site_url('verify-login') ?>">
                <?= csrf_field() ?>
                <?= view('flash_messages'); ?>

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email"
                        placeholder="Enter your email" required>
                </div>

                <!-- Terms & Privacy Checkbox -->
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" name="agree_terms" id="agree_terms" value="1" >
                    <label class="form-check-label" for="agree_terms">
                         I consent to receive conversational and informational SMS from Royal XPay. 
                    Reply STOP to opt-out; Reply Help for support; Message and data rates apply; 
                    Messaging frequency may vary. 
                    See our <a href="#" class="text-primary">Terms & Conditions</a> and 
                    <a href="#" class="text-primary">Privacy Policy</a>.
                    </label>
                </div>

                       <!-- Custom error message -->
  <small id="terms-error" style="display: none !important; font-size:.75rem; line-height:1rem; font-weight:400; padding-left:.25rem; color:#f41010;" 
       class="d-flex align-items-center mt-1">
    <i class="fa fa-exclamation-circle me-1" 
       style="color:#f41010; font-size:.75rem; line-height:1rem;"></i>
    You must agree to the “Terms &amp; Conditions” and “Privacy Policy”.
</small>
                

                
                <!-- Inline Error (below checkbox) -->
                <?php if (session()->getFlashdata('error')): ?>
                    <small class="d-flex align-items-center gap-1 mt-1">
                        <i class="fa fa-exclamation-circle text-danger me-1"></i>
                        <span class="text-danger small">
                            <?= session()->getFlashdata('error') ?>
                        </span>
                    </small>
                <?php endif; ?>

                <!-- Send OTP Button -->
                <div class="d-grid mt-3">
                    <button id="login-btn" class="btn btn-primary" type="submit">
                        Send Verification Code
                    </button>
                </div>
            </form>


                <!-- Mobile Login Form -->
                <form id="form-mobile" style="display:none;" method="post" action="<?= site_url('send-otp') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3" id="mobile-step">
                        <label>Mobile Number</label>
                        <div class="input-group">
                            <select class="form-select" name="country_code" style="max-width: 120px;">
                                <option value="+971" selected>🇦🇪 +971</option>
                                <option value="+91">🇮🇳 +91</option>
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+44">🇬🇧 +44</option>
                            </select>
                            <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter mobile number" required>
                        </div>
                        <button type="button" class="btn btn-primary w-100 mt-3" id="send-otp">Send OTP</button>
                    </div>

                    <div class="mb-3" id="otp-step" style="display:none;">
                        <label>Enter OTP</label>
                        <input type="text" name="otp" maxlength="6" class="form-control" placeholder="------" required>
                        <button type="submit" class="btn btn-success w-100 mt-3">Verify OTP</button>
                    </div>
                </form>

                <!-- Register Link -->
                <div class="text-center mt-4">
                    <p class="text-muted">Don't have an account? <a href="<?= site_url('register'); ?>" class="text-primary fw-bold">Register Now</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
<script>
    // On form submit
// On form submit
$('#form-email').on('submit', function (e) {
    const agree = $('#agree_terms').is(':checked');
    const errorDiv = $('#terms-error');
    let btn = $('#login-btn');

    if (!agree) {
        e.preventDefault();
        errorDiv.show();   // show error if not selected
        return false;
    } else {
        errorDiv.hide();   // hide error if selected
        btn.prop('disabled', true).text('Please wait...');
    }
});

// Hide error once checkbox is ticked
$('#agree_terms').on('change', function () {
    if ($(this).is(':checked')) {
        $('#terms-error').hide();   // ✅ remove error when selected
    }
});



    $(function () {
        $('#tab-email').click(function () {
            $(this).addClass('active');
            $('#tab-mobile').removeClass('active');
            $('#form-email').show();
            $('#form-mobile').hide();
        });

        $('#tab-mobile').click(function () {
            $(this).addClass('active');
            $('#tab-email').removeClass('active');
            $('#form-email').hide();
            $('#form-mobile').show();
        });

        $('#send-otp').click(function () {
            const mobile = $('#mobile').val().trim();
            const countryCode = $('select[name="country_code"]').val();

            if (mobile.length < 7) {
                alert("Enter a valid mobile number");
                return;
            }
            
    // Disable button to prevent multiple clicks
    btn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: "<?= site_url('send-otp') ?>",
                method: "POST",
                data: {
                    country_code: countryCode,
                    mobile: mobile,
                    <?= csrf_token() ?>: "<?= csrf_hash() ?>"
                },
                success: function (res) {
                    $('#mobile-step').hide();
                    $('#otp-step').show();
                },
                error: function () {
                    alert("Something went wrong. Please try again.");
                        btn.prop('disabled', false).text('Send OTP');
                }
            });
        });
    });

    function showHidePassword(groupId) {
        const input = document.querySelector(`#${groupId} input`);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }

    $('#form-email').on('submit', function () {
    let btn = $('#login-btn');
    btn.prop('disabled', true).text('Please wait...');
});
</script>


  <!-- JavaScript -->
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/node-waves/waves.min.js'); ?>"></script>

<script>
  
    </script>


</body>
</html>
