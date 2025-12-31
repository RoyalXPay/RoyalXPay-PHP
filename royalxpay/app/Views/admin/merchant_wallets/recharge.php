<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="container mt-4">
    <h4><?= esc($pagetitle) ?></h4>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
<br /><br /><br />
    <form method="post" action="<?= site_url('manage-wallet/recharge') ?>">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-md-3">
                <label>Amount (AED)</label>
                <input type="number" name="amount" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Transaction Date</label>
                <input type="date" name="transaction_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Mode of Payment</label>
                <select name="payment_mode" class="form-control" id="modeSelect" required>
                    <option value="">Select</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Online Transfer">Online Transfer</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
        </div>

        <div class="row mt-3" id="extraFields" style="display: none;">
            <div class="col-md-3">
                <label>Cheque No / UTR No</label>
                <input type="text" name="utr_no" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Beneficiary Name</label>
                <input type="text" name="beneficiary" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Your Account No</label>
                <input type="text" name="account_no" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Your Bank Name</label>
                <input type="text" name="bank_name" class="form-control">
            </div>
            <div class="col-md-4 mt-2">
                <label>Branch Address</label>
                <input type="text" name="branch_address" class="form-control">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Submit Recharge</button>
            <a href="<?= site_url('manage-wallet/recharge-history') ?>" class="btn btn-outline-dark">Recharge History</a>
        </div>
    </form>

    <hr class="mt-4">
    <h5>RoyalXPay Bank Details:</h5>
    <ul>
        <li><strong>Beneficiary Name:</strong> RoyalXPay</li>
        <li><strong>Account Number:</strong> 01234567890</li>
        <li><strong>Bank Name:</strong> First Abu Dhabi Bank</li>
        <li><strong>IFCS Code:</strong> FAD2323223</li>
        <li><strong>Branch:</strong> Aaliyah Plaza Hypermarket, Musaffah, Abu Dhabi, UAE</li>
    </ul>
</div>

<script>
document.getElementById('modeSelect').addEventListener('change', function() {
    const mode = this.value;
    const extra = document.getElementById('extraFields');
    if (mode === 'Cheque' || mode === 'Online Transfer' || mode === 'Bank Transfer') {
        extra.style.display = 'flex';
    } else {
        extra.style.display = 'none';
    }
});
</script>
<?= $this->endSection() ?>
