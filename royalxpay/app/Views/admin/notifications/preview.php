<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Notifications</li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?php echo $pageTitle; ?></a></li>
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
                            <h3>Notification Details</h3>
                        </div>
                        <div class="col-md-12">
                            <?php echo view('admin/_topmessage'); ?>
                            <div class="table-responsive pt-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td scope="row" width="30%">Title</td>
                                            <td>: <?php echo esc($record['title'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Description</td>
                                            <td>: <?php echo esc($record['description'] ?? 'N/A'); ?></td>
                                        </tr>

                                        <tr>
                                            <td scope="row" width="30%">Attachment</td>
                                            <td>
                                                <img src="<?php echo base_url('public/uploads/notifications/' . trim($record['attachment'])); ?>" alt="Product Image" class="img-fluid" style="max-width: 150px; max-height: 150px; margin-right: 10px;">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td scope="row" width="30%">Created At</td>
                                            <td>: <?php echo esc(date('Y-m-d H:i:s', strtotime($record['created_at'] ?? 'N/A'))); ?></td>
                                        </tr>
                                        <tr>
                                            <td scope="row" width="30%">Updated At</td>
                                            <td>: <?php echo esc(date('Y-m-d H:i:s', strtotime($record['updated_at'] ?? 'N/A'))); ?></td>
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