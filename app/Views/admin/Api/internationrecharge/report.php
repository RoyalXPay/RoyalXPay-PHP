<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <h4 class="mb-4">Internation Recharge Payment Reports</h4>
    <form method="get" action="">
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= esc($_GET['start_date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= esc($_GET['end_date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>Search</label>
            <input type="text" name="search" class="form-control" placeholder="Account No. / Txn ID"
                value="<?= esc($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3" style="margin-top: 32px;">
            <button class="btn btn-primary">Filter</button>
            <a href="<?= site_url('api/aadc/reports') ?>" class="btn btn-secondary">Clear</a>
        </div>
    </div>
</form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Account Number</th>
                <th>Mobile No.</th>
                <th>Amount (AED)</th>
                 <th>Commission</th>
                <th>Status</th>
                <th>Date & Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $txn): ?>
                    <tr>
                        <td><?= esc($txn['transaction_id']) ?></td>
                        <td><?= esc($txn['account_number']) ?></td>
                        <td><?= esc($txn['customer_mobile']) ?></td>
                        <td><?= esc($txn['amount']) ?></td>
                         <td><?= esc($txn['commision']) ?></td>

                        <td><?= esc($txn['status']) ?></td>
                        <td><?= esc(date('d-m-Y H:i:s', strtotime($txn['created_at']))) ?></td>
                       <td>
                            <a href="<?= site_url('api/aadc/receipt/' . $txn['id']) ?>" target="_blank" class="btn btn-sm btn-primary" title="Download Receipt">
                                <i class="mdi mdi-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No transactions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="<?= site_url('api/aadc') ?>" class="btn btn-secondary mt-3">Back to Payment</a>
</div>

<?= $this->endSection() ?>
