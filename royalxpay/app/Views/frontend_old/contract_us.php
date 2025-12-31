<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid px-0">
    <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3654.9931403167843!2d58.20400000000001!3d23.6404167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjPCsDM4JzI1LjUiTiA1OMKwMTInMTQuNCJF!5e0!3m2!1sen!2sin!4v1730294899724!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
<div class="container">
    <div class="py-5 contact-section">
        <div class="row d-flex align-items-start justify-content-between py-5">
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <h3>Get in Touch!</h3>
                <p>Proin tincidunt, lectus eu volutpat mattis, ante metus lacinia tellus, vitae condimentum nulla enim bibendum nibh. Praesent turpis risus.</p>
                <p>J6R3+5J6 Seeb, Oman<br>
                    Monday to Friday: 9am to 8pm<br>
                    <a style="color: #929292;" href="mailto:depot@qodeinteractive.com">depot@qodeinteractive.com</a>
                </p>
                <div class="d-flex align-items-center justify-content-start mt-4">
                    <a href="#" class="facebook" style="padding: 11px 20px; border: 1px solid #3b5998; background-color: #3b5998;"><i class="fa fa-facebook"></i></a>
                    <a href="#" class="facebook ms-3" style="padding: 11px 17px; border: 1px solid #ea4c89; background-color: #ea4c89"><i class="fa fa-instagram"></i></a>
                    <a href="#" class="facebook ms-3" style="padding: 11px 17px; border: 1px solid #55acee; background-color: #55acee;"><i class="fa fa-twitter"></i></a>
                </div>
            </div>
            <div class="col-1"></div>
            <div class="col-sm-12 col-md-6 col-lg-7 col-xl-7">
                <form class="form-horizontal needs-validation" id="contactForm" method="post" action="<?= base_url('save-contact') ?>" novalidate>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="right-menu">
                        <!-- Name -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-input-bg" id="name" name="name" placeholder="Full Name" required>
                            <label for="name">Full Name</label>
                            <div class="invalid-feedback">Full Name is required</div>
                        </div>

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control form-input-bg" id="email" name="email" placeholder="Email" required>
                            <label for="email">Email Address</label>
                            <div class="invalid-feedback">Email Address is required</div>
                        </div>

                        <!-- Phone -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-input-bg" id="phone" name="phone" placeholder="Phone">
                            <label for="phone">Phone Number</label>
                        </div>

                        <!-- Message -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="message" name="message" placeholder="Write a comment..." style="height: 150px" required></textarea>
                            <label for="message">Write a message...</label>
                            <div class="invalid-feedback">Message is required</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex align-items-stretch button-group mb-2 mt-4">
                        <button type="submit" class="btn text-light py-3 px-5 bg-dark border border-0 rounded-0" style="background-color: #080808!important;">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>