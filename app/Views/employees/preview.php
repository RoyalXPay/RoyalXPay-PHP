<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Employees</li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">
                                <?= esc($pageTitle); ?></a></li>
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

                        <!-- Employee Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Employee Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <!-- Employee Info: Name, Email, Phone, etc. -->
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
                                    <p><strong>Department:</strong> <?= esc($record['department']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Designation:</strong> <?= esc($record['designation']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Status:</strong>
                                        <?php if ($record['status'] === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </p>
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