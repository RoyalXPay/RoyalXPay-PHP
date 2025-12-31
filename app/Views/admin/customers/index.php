<?php $permissions = session()->get('permissions'); ?>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        /* Initially hide the search form */
    }
</style>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" style="display:none;">
        <?= session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="page-title-box">
                    <h4 class="font-size-18"><?php echo $pagetitle; ?></h4>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="d-none d-md-block" style="    text-align: end;">
                      <button type="button" data-bs-toggle="modal" data-bs-target="#newCustomerModal" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i>Add Customer
                            </button> 
                            
                </div>
            </div>
        </div>

        <form action="" id="customersearch" method="get">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Start Date</label>
                                    <input class="form-control" name="startDate" type="date" value="<?php echo isset($startDate) ? $startDate : ''; ?>">
                                </div>
                                <div class="col-lg-2">
                                    <label for="endDate">End Date</label>
                                    <input class="form-control" name="endDate" type="date" value="<?php echo isset($endDate) ? $endDate : ''; ?>">
                                </div>
                                <div class="col-lg-4">
                                    <label for="txtsearch">Search</label>
                                    <input class="form-control" name="txtsearch" type="text" value="<?php echo isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search by Name/Mobile/Email/Emirates ID">
                                </div>
                                 <?php if (session()->get('user_type') == 'superadmin'): ?>
                                    <div class="col-lg-3">
                                        <label for="merchant_id">Merchant</label>
                                        <select class="form-control select2" name="merchant_id" id="merchant_id">
                                            <option value="">All Merchants</option>
                                            <?php foreach ($merchants as $merchant): ?>
                                                <option value="<?= esc($merchant['user_id']) ?>" <?= isset($merchantId) && $merchantId == $merchant['user_id'] ? 'selected' : '' ?>>
                                                    <?= esc($merchant['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <div class="col-lg-2">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" name="amount" step="0.01" value="<?= esc($amount ?? '') ?>" placeholder="Min amount">
                                    </div>
                                <div class="col-lg-4" style="margin-top: 27px;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="<?php echo site_url('customers'); ?>" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Emirates ID</th>
                                        <th>Paid Amount</th>
                                        <!-- <th>Address</th> -->
                                        <th>Created By</th>
                                        <th>Joined On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $index => $row): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= esc($row['name']) ?></td>
                                            <td><?= esc($row['phone']) ?></td>
                                            <td><?= esc($row['email']) ?></td>
                                            <td><?= esc($row['emirates_id']) ?></td>
                                            <td><?= esc($row['paid_amount']) ?></td>
                                            <td>
                                                <?= ($row['merchant_type'] === 'superadmin') ? 'Super Admin' : esc($row['merchant_name']) ?>
                                            </td>
                                            <td><?= esc(date('d-m-Y', strtotime($row['created_at']))) ?></td>
                                            <td>
                                                <a href="javascript:void(0);" 
                                                class="btn btn-info btn-sm add-payment-btn"
                                                data-id="<?= $row['id'] ?>"
                                                title="Add Payment">
                                                <i class="fas fa-plus"></i>
                                                </a>
                                                <a href="javascript:void(0);" 
                                                class="btn btn-primary btn-sm edit-btn"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= esc($row['name']) ?>"
                                                data-phone="<?= esc($row['phone']) ?>"
                                                data-email="<?= esc($row['email']) ?>"
                                                data-emirates_id="<?= esc($row['emirates_id']) ?>"
                                                data-created_by="<?= ($row['merchant_type'] === 'superadmin') ? 'Super Admin' : esc($row['merchant_name']) ?>"

                                                data-address="<?= esc($row['address']) ?>"
                                                title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>                                                
                                                <a href="javascript:void(0);"
                                                class="btn btn-success btn-sm preview-btn"
                                                data-name="<?= esc($row['name']) ?>"
                                                data-phone="<?= esc($row['phone']) ?>"
                                                data-email="<?= esc($row['email']) ?>"
                                                data-created_by="<?= ($row['merchant_type'] === 'superadmin') ? 'Super Admin' : esc($row['merchant_name']) ?>"

                                                data-emirates_id="<?= esc($row['emirates_id']) ?>"
                                                data-address="<?= esc($row['address']) ?>"
                                                title="Preview">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= site_url('customers/payment_history/' . $row['id']); ?>" 
                                                class="btn btn-warning btn-sm" 
                                                title="Transaction History">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                                <a href="<?= site_url('customers/delete'); ?>" onclick="return confirm('Are you sure you want to delete this customer?');" data-id="<?= $row['id'] ?>" class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/customers/create') ?>
<?= $this->include('admin/customers/edit') ?>
<?= $this->include('admin/customers/preview') ?>
<?= $this->include('admin/customers/add_payment') ?>
<script>
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this customer?')) {
            $.post('<?php echo site_url('customers/delete'); ?>', { id }, function(response) {
                location.reload();
            });
        }
    });


    $(document).on('click', '.edit-btn', function () {
    $('#edit_id').val($(this).data('id'));
    $('#edit_name').val($(this).data('name'));
    $('#edit_phone').val($(this).data('phone'));
    $('#edit_email').val($(this).data('email'));
    $('#edit_emirates_id').val($(this).data('emirates_id'));
    $('#edit_address').val($(this).data('address'));

    $('#editCustomerModal').modal('show');
});



$(document).ready(function () {
    $('#editCustomerForm').on('submit', function (e) {
        e.preventDefault(); // Prevent default submission

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            dataType: 'json',
            success: function (res) {
                const msgBox = $('#editModalMsg');
                msgBox.show();

                if (res.status === 'success') {
                    msgBox
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(res.message);

                    // Optional: close modal after 2s and reload list
                    setTimeout(() => {
                        $('#editCustomerModal').modal('hide');
                        msgBox.hide();
                        location.reload(); // reload table
                    }, 2000);
                } else {
                    msgBox
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html(res.message);
                }
            },
            error: function () {
                $('#editModalMsg')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .html('Something went wrong.')
                    .show();
            }
        });
    });
});

</script>

<script>
$(document).ready(function () {
    // Preview Modal Trigger
    $(document).on('click', '.preview-btn', function () {
        $('#preview_name').text($(this).data('name'));
        $('#preview_phone').text($(this).data('phone'));
        $('#preview_email').text($(this).data('email'));
        $('#preview_emirates_id').text($(this).data('emirates_id'));
        $('#preview_address').text($(this).data('address'));
            $('#preview_created_by').text($(this).data('created_by'));

        $('#previewCustomerModal').modal('show');
    });

    
    // Add Payment Modal Trigger
  $(document).on('click', '.add-payment-btn', function () {
    const customerId = $(this).data('id');
    $('#payment_id').val(customerId); // set hidden field
    $('#addPaymentModal').modal('show');
});
});
</script>

<?= $this->endSection() ?>
