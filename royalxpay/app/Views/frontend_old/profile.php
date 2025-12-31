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
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="border-bottom pb-2">
                            <h2>My Profile</h2>
                        </div>

                        <div class="col-8">
                            <?= view('admin/_topmessage') ?>

                            <div class="table-responsive pt-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td width="30%"><strong>Name:</strong></td>
                                            <td><?= esc($profiledata['name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%"><strong>Mobile:</strong></td>
                                            <td><?= esc($profiledata['phone']) ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%"><strong>Email:</strong></td>
                                            <td><?= esc($profiledata['email']) ?></td>
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