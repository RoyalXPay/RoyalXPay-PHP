<?= $this->extend('admin/layouts/main'); ?> <!-- or whatever your base layout is -->
<?= $this->section('content'); ?>

<div class="page-content">
    <div class="container-fluid">
        <h4><?= $pagetitle; ?></h4>
        <?php echo view('admin/_topmessage'); ?>
        <form method="post" action="<?= site_url('update-wallet'); ?>">
            <input type="hidden" name="user_id" value="<?= $edit['user_id']; ?>">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" value="<?= esc($edit['name']); ?>" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label>Wallet Amount (AED)</label>
                <input type="number" name="wallet" step="0.01" value="" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Update Wallet</button>
            <a href="<?= site_url('manage-wallet'); ?>" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
