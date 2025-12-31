<?php $permissions = session()->get('permissions'); ?>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .select2-container {
    z-index: 9999 !important;
}
    </style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Merchant</a></li>
                        <li class="breadcrumb-item active">Merchant List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#newCustomerModal" 
                                    class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add Merchant
                            </button>
                        <?php } ?>

                        <a class="btn btn-secondary" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Search Form -->
        <div class="search-form">
            <form method="get" action="<?= site_url('merchant'); ?>">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <label for="txtsearch">Name / Mobile</label>
                                <input class="form-control" name="txtsearch" type="text"
                                       value="<?= esc($txtsearch ?? '') ?>" placeholder="Search by name or mobile">
                            </div>
                            <div class="col-lg-2">
                                <label for="startDate">Start Date</label>
                                <input type="date" class="form-control" name="startDate" value="<?= esc($startDate ?? '') ?>">
                            </div>
                            <div class="col-lg-2">
                                <label for="endDate">End Date</label>
                                <input type="date" class="form-control" name="endDate" value="<?= esc($endDate ?? '') ?>">
                            </div>
                            <div class="col-lg-2">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" name="amount" step="0.01"
                                       value="<?= esc($amount ?? '') ?>" placeholder="Min amount">
                            </div>
                            <div class="col-lg-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Search</button>
                                <a href="<?= site_url('merchant'); ?>" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- End Search Form -->

        <!-- Merchant Table -->
        <div class="card mt-3">
            <?= view('admin/_topmessage'); ?>
            <div class="card-body">
                <?php if ($pagination["totalRecords"] > 0) { ?>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">Sl</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Phone</th>
                                     <th class="text-center">Address</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Gender</th>
                                    <th class="text-center">Company Name</th>
                                    <th class="text-center">Logo</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($results as $customer) { ?>
                                    <tr>
                                        <td><?= ++$startLimit; ?></td>
                                        <td><?= esc($customer->name); ?></td>
                                        <td><?= esc($customer->email); ?></td>
                                        <td><?= esc($customer->phone); ?></td>
                                        <td><?= esc($customer->address); ?></td>
                                        <td><?= esc($customer->wallet); ?></td>
                                        <td><?= esc($customer->gender); ?></td>
                                          <td><?= esc($customer->company_name); ?></td>
                                     <td>
    <?php if (!empty($customer->logo)) : ?>
        <img src="<?= base_url($customer->logo); ?>" 
             alt="Logo" 
             style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
    <?php else : ?>
        <span class="text-muted">No Logo</span>
    <?php endif; ?>
</td>
                                        <td><?= esc($customer->status); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center flex-wrap">

                                             

                                                <!-- Edit -->
                                                    <a href="#" class="btn btn-success m-1 editBtn" 
                                                       data-id="<?= $customer->user_id ?>" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                <!-- Preview -->
                                                <a href="<?= site_url("merchant/preview/" . $customer->user_id) ?>" 
                                                   class="btn btn-warning m-1" title="Preview">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Delete -->
                                                    <button type="button" class="btn btn-danger m-1 dltBtn"
                                                            data-id="<?= $customer->user_id ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                            </div>

                                            <!-- Privilege Modal Content -->
                                            <!-- Privileges Modal -->
                               <!-- Privileges Modal -->






                                            <!-- End Privilege Modal -->

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['totalRecords']) { ?>
                        <br>
                        <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
                    <?php } ?>
                <?php } else { ?>
                    <?= view('admin/_noresult'); ?>
                <?php } ?>
            </div>
        </div>
        <!-- End Merchant Table -->

    </div>
</div>

<?= $this->include('merchant/create') ?>

<!-- Scripts -->
<script>
  
$(document).ready(function () {
    $('#datatable').DataTable({
        dom: 'Bfrtip',
        searching: false,
        paging: false,
        info: false
    });

    // Tooltip
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Reset form when modal closes
    $('#newCustomerModal').on('hidden.bs.modal', function () {
        $('#customerForm')[0].reset();
        $('#modalMessages').html('');
        $('#customerId').val('');
        $('#submitButton').text('Save');
        $('#newCustomerModalLabel').text('Add Merchant');

          // Clear logo preview
    $('#previewImage').attr('src', '');
    $('#logoPreview').hide();
    });

    // Live logo preview on file select
$('#logo').on('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#previewImage').attr('src', e.target.result);
            $('#logoPreview').show();
        };
        reader.readAsDataURL(file);
    } else {
        $('#previewImage').attr('src', '');
        $('#logoPreview').hide();
    }
});


    // Save Merchant Form
    $('#customerForm').on('submit', function (e) {
        e.preventDefault();
let isEditMode = $('#customerId').val() !== ""; // check if we're editing

        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
       // If it's not edit OR password field is filled, validate
if (!isEditMode || password.length > 0) {
    if (password === "" || confirmPassword === "") {
        $('#modalMessages').html('<div class="alert alert-danger">Password fields cannot be empty.</div>');
        return;
    }
    if (password !== confirmPassword) {
        $('#modalMessages').html('<div class="alert alert-danger">Password and Confirm Password do not match.</div>');
        return;
    }
}

        var formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: "<?= site_url('merchant/save') ?>",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#modalMessages').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(() => { $('#newCustomerModal').modal('hide'); location.reload(); }, 2000);
                } else {
                    let errorMsg = '<ul>';
                    $.each(response.errors, function (key, val) { errorMsg += '<li>' + val + '</li>'; });
                    errorMsg += '</ul>';
                    $('#modalMessages').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            },
            error: function () {
                $('#modalMessages').html('<div class="alert alert-danger">Unexpected error, try again.</div>');
            }
        });
    });

    // Edit Merchant
    $('.editBtn').on('click', function () {
        let customerId = $(this).data('id');
        $.ajax({
            url: '<?= site_url('merchant/edit') ?>/' + customerId,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                console.log("EDIT DATA:", data);
                $('#customerId').val(data.user_id);
                $('#username').val(data.username);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#gender').val(data.gender);
                $('#customerStatus').val(data.status);
                $('#notes').val(data.notes);
                $('#company_name').val(data.company_name); // ✅ FIXED
    $('#altMobileNumber').val(data.alt_mobile_number); // ✅ FIXED
                $('#submitButton').text('Update');
                $('#newCustomerModalLabel').text('Edit Merchant');
                 // Show logo preview if logo exists
  // Safely pass base URL from PHP to JS
const baseURL = "<?= base_url() ?>";

// Then inside your success callback:
if (data.logo) {
    let fullLogoURL = baseURL + data.logo.replace(/^\/+/, '');
    console.log("LOGO URL:", fullLogoURL);
    $('#previewImage').attr('src', fullLogoURL);
    $('#logoPreview').show();
} else {
    $('#previewImage').attr('src', '');
    $('#logoPreview').hide();
}

    $('#newCustomerModal').modal('show');

                
            }
        });
    });

    // Delete Merchant
    $('.dltBtn').click(function () {
        let dataID = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "Deleted data cannot be restored!",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                $.post("<?= site_url('merchant/delete') ?>", { customerId: dataID }, function (response) {
                    if (response.status === 'success') {
                        swal(response.message, { icon: "success", timer: 2000, buttons: false });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        swal("Error!", response.message, "error");
                    }
                }, 'json');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
