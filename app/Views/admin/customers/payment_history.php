<?php $permissions = session()->get('permissions'); ?>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        /* Initially hide the search form */
    }
</style>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" style="display:none;">
        <?= session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="page-title-box">
                    <h4 class="font-size-18"><?php echo $pagetitle; ?></h4>
                </div>
                <a href="<?= site_url('customers'); ?>" class="btn btn-secondary mt-2">Back to Customers</a>

            </div>
           
        </div>

        <form action="" id="customersearch">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Start Date</label>
                                    <input class="form-control" name="startDate" type="date" value="<?php echo isset($startDate) ? $startDate : ''; ?>">
                                </div>
                                <div class="col-lg-2">
                                    <label for="endDate">End Date</label>
                                    <input class="form-control" name="endDate" type="date" value="<?php echo isset($endDate) ? $endDate : ''; ?>">
                                </div>
                                <div class="col-lg-4">
                                    <label for="txtsearch">Search</label>
                                    <input class="form-control" name="txtsearch" type="text" value="<?php echo isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search by Name/Mobile/Email/Emirates ID">
                                </div>
                                <div class="col-lg-4" style="margin-top: 27px;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="<?php echo site_url('customers'); ?>" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Mobile No</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>transaction By</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php foreach ($transactions as $i => $tx): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= ucfirst($tx['name']) ?></td>
                                        <td><?= ucfirst($tx['phone']) ?></td>
                                        <td><?= number_format($tx['amount'], 2) ?> AED</td>
                                        <td><?= ucfirst($tx['transaction_type']) ?></td>
                                        <td><?= esc($tx['wallet_by']) ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime($tx['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/customers/create') ?>
<?= $this->include('admin/customers/edit') ?>
<?= $this->include('admin/customers/preview') ?>
<?= $this->include('admin/customers/add_payment') ?>




<?= $this->endSection() ?>
