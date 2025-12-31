<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="alert alert-success mt-5">
        <h4>Payment Successful</h4>
        <p>Transaction ID: <strong><?= $txn_id ?></strong></p>
        <p>Status: <strong><?= $status ?></strong></p>
        <a href="<?= site_url('admin/aadc') ?>" class="btn btn-primary">Back</a>
    </div>
</div>
<?= $this->endSection() ?>
