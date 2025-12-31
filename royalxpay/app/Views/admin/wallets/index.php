<?= $this->extend('admin/layouts/main'); ?> <!-- or whatever your base layout is -->
<?= $this->section('content'); ?>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title and Add Wallet Button -->
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="page-title-box">
                    <h4 class="font-size-18"><?= $pagetitle; ?></h4>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="float-right d-none d-md-block" style="    text-align: right;
}">
                    <a class="btn btn-primary waves-effect waves-light" href="<?= site_url('add-wallet'); ?>">
                        <i class="ion ion-md-add-circle-outline"></i> Add Wallet Amount
                    </a>
                </div>
            </div>
        </div>


        <?php echo view('admin/_topmessage'); ?>

         <div class="search-form">
            <form action="" id="walletsearch" method="get">
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
                                 <?php if (session()->get('user_type') == 'superadmin'): ?>
                                    <div class="col-lg-3">
                                        <label for="merchant_id">Merchant</label>
                                        <select class="form-control select2" name="merchant_id" id="merchant_id">
                                            <option value="">All Merchants</option>
                                            <?php foreach ($merchants as $merchant): ?>
                                                <option value="<?= esc($merchant['user_id']) ?>" <?= isset($merchantId) && $merchantId == $merchant['user_id'] ? 'selected' : '' ?>>
                                                    <?= esc($merchant['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <div class="col-lg-2">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" name="amount" step="0.01" value="<?= esc($amount ?? '') ?>" placeholder="Min amount">
                                    </div>
                                <div class="col-lg-4" style="margin-top: 27px;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="<?php echo site_url('manage-wallet'); ?>" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($users)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-centered table-bordered table-nowrap mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Mobile</th>
                                            <th class="text-center">User Type</th>
                                            <th class="text-center">Wallet (AED)</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $k => $u): 
                                        $walletamount = $u['wallet'] ?? 0;
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $k + 1; ?></td>
                                                <td class="text-center"><?= esc($u['name']); ?></td>
                                                <td class="text-center"><?= esc($u['phone']); ?></td>
                                                <td class="text-center"><?= ucfirst($u['user_type']); ?></td>
                                                <td class="text-center"><?= number_format((float)$walletamount, 2); ?></td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-primary" href="<?= site_url('edit-wallet/' . $u['user_id']); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
