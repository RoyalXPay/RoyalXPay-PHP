<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <h4>Employee Upload Preview</h4>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('employees/save-bulk') ?>" method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Gender</th>
                                    <th>DOB</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($previewData as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <?php foreach ($row as $key => $value): ?>
                                            <td><?= esc($value) ?>
                                                <input type="hidden" name="employees[<?= $i ?>][<?= $key ?>]" value="<?= esc($value) ?>">
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Save & Submit</button>
                    <a href="<?= site_url('employees') ?>" class="btn btn-secondary mt-3">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
