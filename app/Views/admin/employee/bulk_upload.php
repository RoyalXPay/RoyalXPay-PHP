<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <h4>Bulk Employee Upload</h4>
        <div class="card">
            <div class="card-body">
                <p>Step 1: <a href="<?= site_url('employees/download-format') ?>" class="btn btn-sm btn-success">Download Format</a></p>
                <form action="<?= site_url('employees/import-preview') ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Upload Excel File (.xls or .xlsx)</label>
                        <input type="file" name="excel_file" accept=".xls,.xlsx" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload & Preview</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
