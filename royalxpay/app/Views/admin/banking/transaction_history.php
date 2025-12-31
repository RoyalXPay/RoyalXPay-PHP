<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-form {
        display: none;
    }
</style>

<div class="page-content">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Bank Transfer History</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="<?= current_url(); ?>" method="get">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Start Date</label>
                                    <input type="date" name="startDate" value="<?= esc($startDate ?? '') ?>" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>End Date</label>
                                    <input type="date" name="endDate" value="<?= esc($endDate ?? '') ?>" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Search</label>
                                    <input type="text" name="txtsearch" value="<?= esc($txtsearch ?? '') ?>" class="form-control" placeholder="Search by Name / Mobile / Email">
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Search</button>
                                    <a href="<?= site_url('banking/transfer-to-bank'); ?>" class="btn btn-secondary">
                                        <i class="mdi mdi-refresh"></i> Clear
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Results Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>Amount</th>
                                    <th>Bank Name</th>
                                    <th>Account No</th>
                                    <th>IFSC Code</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transactions)) : ?>
                                    <?php foreach ($transactions as $index => $row) : ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= esc($row->user_name); ?></td>
                                            <td>₹<?= number_format($row->amount, 2); ?></td>
                                            <td><?= esc($row->bank_name); ?></td>
                                            <td><?= esc($row->account_no); ?></td>
                                            <td><?= esc($row->ifsc_code); ?></td>
                                            <td>
                                                <span class="badge bg-<?= $row->status === 'success' ? 'success' : 'warning'; ?>">
                                                    <?= ucfirst($row->status); ?>
                                                </span>
                                            </td>
                                            <td><?= date('d-m-Y H:i', strtotime($row->created_at)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No transactions found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
/                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
