<?= $this->extend('admin/layouts/main'); ?> <!-- or whatever your base layout is -->
<?= $this->section('content'); ?>
<div class="page-content">
    <div class="container-fluid">
        <h4><?= esc($pagetitle); ?> </h4>
        <!-- <a href="<?= site_url('manage-wallet'); ?>" class="btn btn-secondary mb-3">Back</a> -->
         <form method="get" class="mb-3">
    <div class="row">
        <div class="col-md-2">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?= esc($startDate ?? '') ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <label>End Date</label>
            <input type="date" name="end_date" value="<?= esc($endDate ?? '') ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Customer Name / Mobile</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" class="form-control" placeholder="Name or Mobile">
        </div>
        <div class="col-md-2">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="">All</option>
                <option value="credit" <?= $type == 'credit' ? 'selected' : '' ?>>Credit</option>
                <option value="debit" <?= $type == 'debit' ? 'selected' : '' ?>>Debit</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Amount</label>
            <input type="number" name="amount" value="<?= esc($amount ?? '') ?>" class="form-control" step="0.01">
        </div>
        <div class="col-md-1" style="margin-top: 30px;">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

        <div class="table-responsive">
           <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Amount (AED)</th>
                        <th>Type</th>
                        <th>Date/Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $i => $txn): ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td>
                                    <?php
                                        if ($txn['wallet_by'] == 'superadmin') {
                                            echo "Superadmin";
                                        } else {
                                            echo esc($txn['customer_name'] ?? '—');
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        
                                            echo esc($txn['customer_phone'] ?? '—');
                                        
                                    ?>
                                </td>


                                <td><?= number_format((float)$txn['amount'], 2); ?></td>
                                <td>
                                <span style="color: <?= $txn['transaction_type'] === 'credit' ? 'green' : 'red'; ?>;">
                                        <?= ucfirst($txn['transaction_type']); ?>
                                    </span>
                                </td>
                                <td><?= date('Y-m-d H:i:s', strtotime($txn['created_at'])); ?></td>
                                 <td>
                                    <!-- ✅ Details Button -->
                                    <button class="btn btn-info btn-sm view-transaction"
                                        data-amount="<?= $txn['amount']; ?>"
                                        data-type="<?= $txn['transaction_type']; ?>"
                                        data-date="<?= $txn['created_at']; ?>"
                                        data-wallet_by="<?= $txn['wallet_by'] ?? 'N/A'; ?>"
                                        data-customer_id="<?= $txn['customer_id'] ?? 'N/A'; ?>"
                                        data-customer_name="<?= $txn['name'] ?? 'N/A'; ?>"
                                        data-user_id="<?= $txn['user_id']; ?>">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No transactions found.</td></tr>
                    <?php endif; ?>
                </tbody>

            </table>

            <!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Amount:</strong> <span id="modalAmount"></span> AED</p>
                <p><strong>Type:</strong> <span id="modalType"></span></p>
                <p><strong>Date/Time:</strong> <span id="modalDate"></span></p>
                <p><strong>From / To:</strong> <span id="modalWalletBy"></span></p>
                <p><strong>Customer ID:</strong> <span id="modalCustomer"></span></p>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>
</div>   
<script>
$(document).on('click', '.view-transaction', function () {
    const amount = $(this).data('amount');
    const type = $(this).data('type');
    const date = $(this).data('date');
    const walletBy = $(this).data('wallet_by');
    const customerId = $(this).data('customer_name');

    $('#modalAmount').text(amount);
    $('#modalType').text(type.charAt(0).toUpperCase() + type.slice(1));
    $('#modalDate').text(date);
    $('#modalWalletBy').text(walletBy);
    $('#modalCustomer').text(customerId !== 'N/A' ? customerId : '—');

    $('#transactionDetailModal').modal('show');
});
</script>

<?= $this->endSection(); ?>