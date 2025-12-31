<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Company Info</a></li>
                        <li class="breadcrumb-item active">Invoice Details</li>
                    </ol>

                    <div class="page-title-right">
                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <style>
            .form-group {
                margin-bottom: 10px;
            }

            .img-preview {
                max-width: 200px;
                /* Adjust as needed */
                margin-top: 10px;
                display: none;
            }
        </style>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><i class="fas fa-code-branch"></i> Create Invoice Details</h4>
                        <p class="card-subtitle mb-2 text-muted">Fill in the details below to create a new Invoice Details.</p>
                        <form class="custom-validation" method='post' action="<?= site_url('save-company-info'); ?>" enctype='multipart/form-data'>
                            <?= csrf_field(); ?>
                            <?php echo view('flash_messages'); ?>

                            <input type="hidden" name="id" value="<?= $company_info['id'] ?? ''; ?>">
                            <div class="form-mobile-laptop">

                                <!-- Citylight Logo -->
                                <div class="form-group row">
                                    <label for="citylight_logo" class="col-sm-2 col-form-label">Citylight Logo</label>
                                    <div class="col-sm-6">
                                        <input type="file" name="citylight_logo" id="citylight_logo" class="form-control" accept="image/*" onchange="previewImage(event, 'logoPreview')">
                                        <img id="logoPreview" class="img-preview" alt="Logo Preview" src="<?= !empty($company_info['citylight_logo']) ? base_url('uploads/company/' . $company_info['citylight_logo']) : ''; ?>">
                                    </div>
                                </div>

                                <!-- Citylight Address -->
                                <div class="form-group row">
                                    <label for="citylight_address" class="col-sm-2 col-form-label">Citylight Address</label>
                                    <div class="col-sm-6">
                                        <textarea name="citylight_address" id="citylight_address" class="form-control" placeholder="Enter Citylight Address"><?= $company_info['citylight_address'] ?? ''; ?></textarea>
                                    </div>
                                </div>

                                <!-- Contact Details -->
                                <div class="form-group row">
                                    <label for="contact_details" class="col-sm-2 col-form-label">Contact Details</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="contact_details" id="contact_details" required class="form-control" placeholder="Enter Contact Details" value="<?= $company_info['contact_details'] ?? ''; ?>">
                                    </div>
                                </div>

                                <!-- Citylight Email -->
                                <div class="form-group row">
                                    <label for="citylight_email" class="col-sm-2 col-form-label">Citylight Email</label>
                                    <div class="col-sm-6">
                                        <textarea name="citylight_email" id="citylight_email" class="form-control" placeholder="Enter Email ID"><?= $company_info['citylight_email'] ?? ''; ?></textarea>
                                    </div>
                                </div>

                                <!-- Bank Details -->
                                <div class="form-group row">
                                    <label for="bank_details" class="col-sm-2 col-form-label">Bank Details</label>
                                    <div class="col-sm-6">
                                        <textarea name="bank_details" id="bank_details" class="form-control" placeholder="Enter Bank Details"><?= $company_info['bank_details'] ?? ''; ?></textarea>
                                    </div>
                                </div>

                                <!-- Stamp Upload -->
                                <div class="form-group row">
                                    <label for="stamp" class="col-sm-2 col-form-label">Stamp</label>
                                    <div class="col-sm-6">
                                        <input type="file" name="stamp" id="stamp" class="form-control" accept="image/*" onchange="previewImage(event, 'stampPreview')">
                                        <img id="stampPreview" class="img-preview" alt="Stamp Preview" src="<?= !empty($company_info['stamp']) ? base_url('uploads/company/' . $company_info['stamp']) : ''; ?>">
                                        <?php if (!empty($company_info['stamp'])): ?>
                                            <a href="<?= base_url('uploads/company/' . $company_info['stamp']) ?>" class="btn btn-link" download>Download Current Stamp</a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Signature Upload -->
                                <div class="form-group row">
                                    <label for="signature" class="col-sm-2 col-form-label">Signature</label>
                                    <div class="col-sm-6">
                                        <input type="file" name="signature" id="signature" class="form-control" accept="image/*" onchange="previewImage(event, 'signaturePreview')">
                                        <img id="signaturePreview" class="img-preview" alt="Signature Preview" src="<?= !empty($company_info['signature']) ? base_url('uploads/company/' . $company_info['signature']) : ''; ?>">
                                        <?php if (!empty($company_info['signature'])): ?>
                                            <a href="<?= base_url('uploads/company/' . $company_info['signature']) ?>" class="btn btn-link" download>Download Current Signature</a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Return Policies -->
                                <div class="form-group row">
                                    <label for="return_policies" class="col-sm-2 col-form-label">Return Policies</label>
                                    <div class="col-sm-6">
                                        <textarea name="return_policies" id="return_policies" class="form-control" placeholder="Enter Return Policies"><?= $company_info['return_policies'] ?? ''; ?></textarea>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <!-- <div class="form-group row">
                                    <label for="terms_and_conditions" class="col-sm-2 col-form-label">Terms and Conditions</label>
                                    <div class="col-sm-6">
                                        <textarea name="terms_and_conditions" id="terms_and_conditions" class="form-control" placeholder="Enter Terms and Conditions"></?= $company_info['terms_and_conditions'] ?? ''; ?></textarea>
                                    </div>
                                </div> -->

                                <!-- Submit Button -->
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
</div> <!-- End Page-content -->

<script>
    function previewImage(event, previewId) {
        const file = event.target.files[0];
        const reader = new FileReader();
        const imgPreview = document.getElementById(previewId);

        reader.onload = function() {
            imgPreview.src = reader.result;
            imgPreview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, keep the current src
            imgPreview.src = imgPreview.src;
            imgPreview.style.display = 'block';
        }
    }

    // To initialize the preview on page load for existing images
    window.onload = function() {
        const logoPreview = document.getElementById('logoPreview');
        const stampPreview = document.getElementById('stampPreview');
        const signaturePreview = document.getElementById('signaturePreview');

        if (logoPreview.src) {
            logoPreview.style.display = 'block';
        }
        if (stampPreview.src) {
            stampPreview.style.display = 'block';
        }
        if (signaturePreview.src) {
            signaturePreview.style.display = 'block';
        }
    };
</script>

<?= $this->endSection() ?>