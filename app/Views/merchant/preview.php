<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Merchant</li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?= esc($pageTitle); ?></a></li>
                    </ol>

                    <div class="page-title-right">
                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Customer Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Merchant Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <!-- Customer Info: Name, Email, Phone, etc. -->
                                <div class="col-md-4">
                                    <p><strong>Name:</strong> <?= esc($record['name']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Email:</strong> <?= esc($record['email']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Phone:</strong> <?= esc($record['phone']); ?></p>
                                </div>
                              
                               
                                <div class="col-md-4">
                                    <p><strong>Alternate Mobile Number:</strong> <?= esc($record['alt_mobile_number']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Status:</strong>
                                        <?php if ($record['status'] === 'active'): ?>
                                            <span class="badge bg-success"><?= esc($record['status']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?= esc($record['status']); ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Notes:</strong> <?= esc($record['notes']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Created At:</strong> <?= esc(date('Y-m-d H:i:s', strtotime($record['created_at']))); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Updated At:</strong> <?= esc(date('Y-m-d H:i:s', strtotime($record['updated_at']))); ?></p>
                                </div>
   <div class="col-md-4">
                                    <p><strong>Company name :</strong> <?= esc($record['company_name']); ?></p>
                                </div>

                                </div>
   <div class="col-md-4">
                                    <p><strong>Address :</strong> <?= esc($record['address']); ?></p>
                                </div>
                                  <div class="col-md-4">
                                    <p><strong>Logo:</strong>   <?php if (!empty($record['logo'])) : ?>
        <img src="<?= base_url($record['logo']); ?>" 
             alt="Logo" 
             style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
    <?php else : ?>
        <span class="text-muted">No Logo</span>
    <?php endif; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div><!-- End Page-content -->
<?= $this->endSection() ?>