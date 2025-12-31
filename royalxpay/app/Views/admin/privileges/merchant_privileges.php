<?= $this->extend('admin/layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="page-content">
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-xl-8 mt-4">
                <div class="card shadow-sm">
                    
                    <div class="card-body p-4">
                        <div class="container-fluid mt-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= site_url('admin/privileges'); ?>" class="btn btn-secondary">
            ← Back to Assign Privileges
        </a>
    </div>

    <!-- Existing content like table of merchant privileges -->

                        <h4 class="mb-3">Show Merchant Privileges</h4>

                        <form method="get" action="<?= site_url('admin/privileges/merchant'); ?>" class="mb-4">
                            <div class="input-group">
                                <select name="merchant_id" class="form-control" required>
                                    <option value="">Select Merchant</option>
                                    <?php foreach($merchants as $m): ?>
                                        <option value="<?= $m['user_id']; ?>" <?= isset($selectedMerchant) && $selectedMerchant['user_id']==$m['user_id'] ? 'selected' : '' ?>>
                                            <?= esc($m['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary">Show</button>
                            </div>
                        </form>

                        <?php if($selectedMerchant): ?>
                            <form method="post" action="<?= site_url('admin/privileges/merchant/update'); ?>">
                                <input type="hidden" name="merchant_id" value="<?= $selectedMerchant['user_id']; ?>">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>Submodule</th>
                                            <th>Sub-Submodule</th>
                                            <th>Add</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            <th>View</th>
                                            <!-- <th>Download</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($privileges as $p): ?>
                                        <tr>
                                            <td><?= esc($p['module_name']); ?></td>
                                            <td><?= esc($p['submodule_name']); ?></td>
                                            <td><?= esc($p['sub_submodule_name']); ?></td>
                                            <td><input type="checkbox" name="privilege_id[<?= $p['id']; ?>][can_add]" <?= $p['can_add'] ? 'checked' : '' ?>></td>
                                            <td><input type="checkbox" name="privilege_id[<?= $p['id']; ?>][can_edit]" <?= $p['can_edit'] ? 'checked' : '' ?>></td>
                                            <td><input type="checkbox" name="privilege_id[<?= $p['id']; ?>][can_delete]" <?= $p['can_delete'] ? 'checked' : '' ?>></td>
                                            <td><input type="checkbox" name="privilege_id[<?= $p['id']; ?>][can_view]" <?= $p['can_view'] ? 'checked' : '' ?>></td>
                                            <!-- <td><input type="checkbox" name="privilege_id[<?= $p['id']; ?>][can_download]" <?= $p['can_download'] ? 'checked' : '' ?>></td> -->
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <button type="submit" class="btn btn-success mt-3">Update Privileges</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
