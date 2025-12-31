<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="container mt-4">
    <h4><?= esc($pagetitle) ?></h4>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount (AED)</th>
                <th>Payment Mode</th>
                <th>Status</th>
                <th>Date/Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($wallets as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= number_format($row['amount'], 2) ?></td>
                    <td><?= esc($row['wallet_by']) ?></td>
                    <td><span class="badge bg-success">Success</span></td>
                    <td><?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
