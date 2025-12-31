<?= $this->extend('admin/layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid mt-4">
    <h4>Select Merchant to View Privileges</h4>
    <form method="post" action="<?= site_url('admin/privileges/merchant'); ?>">
        <div class="row">
            <div class="col-md-4">
                <select name="merchant_id" class="form-control" required>
                    <option value="">-- Select Merchant --</option>
                    <?php foreach($merchants as $m): ?>
                        <option value="<?= $m['user_id']; ?>"><?= esc($m['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Show</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>
