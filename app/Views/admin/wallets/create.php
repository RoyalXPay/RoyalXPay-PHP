<?= $this->extend('admin/layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="page-content">
    <div class="container-fluid">
        <h4><?= $pagetitle; ?></h4>
        <?php echo view('admin/_topmessage'); ?>

        <form method="post" action="<?= site_url('store-wallet'); ?>">
            <div class="form-group">
                <label>Select Merchant/Superadmin</label>
                <select name="user_id" class="form-control form-control-sm" required style="max-width: 300px;">
                    <option value="">-- Select Merchant/Superadmin --</option>
                    <?php
                    $userModel = new \App\Models\UsersModel();

                    // Get superadmins first
                    $superadmins = $userModel->where('user_type', 'superadmin')->findAll();

                    // Then get merchants
                    $merchants = $userModel->where('user_type', 'merchant')->findAll();

                    // Merge with superadmins first
                    $users = array_merge($superadmins, $merchants);

                    foreach ($users as $user): ?>
                        <option value="<?= $user['user_id']; ?>">
                            <?= esc($user['name']) ?> <span style="font-weight: 500;">(<?= $user['user_type'] ?>)</span>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label>Wallet Amount to Add (AED)</label>
                <input type="number" name="wallet" class="form-control form-control-sm" step="0.01" required style="max-width: 302px;">
            </div>
<br />
            <button type="submit" class="btn btn-primary btn-sm">Add Amount</button>
            <a href="<?= site_url('manage-wallet'); ?>" class="btn btn-secondary btn-sm">Back</a>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
