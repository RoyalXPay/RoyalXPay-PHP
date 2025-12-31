<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Companies</li>
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
                        <div class="col-12 border-bottom mb-4">
                            <h3>Company Details</h3>
                        </div>
                        <div class="col-md-12">
                            <?= view('admin/_topmessage'); ?>
                            <div class="table-responsive pt-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td scope="row" width="30%">Company Name</td>
                                            <td>: <?= esc($record['company_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Email</td>
                                            <td>: <?= esc($record['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Mobile Number</td>
                                            <td>: <?= esc($record['mobile_number']); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Alternate Mobile Number</td>
                                            <td>: <?= esc($record['alt_mobile_number']); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Status</td>
                                            <td>: 
                                                <?php if ($record['status'] === 'active'): ?>
                                                    <span class="badge bg-success"><?= esc($record['status']); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger"><?= esc($record['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td scope="row" width="30%">Address</td>
                                            <td>: <?= esc($record['address']); ?></td>
                                        </tr>

                                        <tr>
                                            <td scope="row" width="30%">Created At</td>
                                            <td>: <?= esc(date('Y-m-d H:i:s', strtotime($record['created_at']))); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Updated At</td>
                                            <td>: <?= esc(date('Y-m-d H:i:s', strtotime($record['updated_at']))); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div><!-- End Page-content -->
<?= $this->endSection() ?>
