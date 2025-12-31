<?php $permissions = session()->get('permissions'); ?>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-form {
        display: none;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                        <li class="breadcrumb-item active">Bank Details List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <?php if (empty($results)) { ?>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#bankDetailsModal" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus"></i> Add New
                                </button>
                            <?php } ?>
                        <?php } ?>
                        <button id="toggleSearchBtn" type="button" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-magnify"></i> Search
                        </button>
                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?= view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Sl</th>
                                            <th class="text-left">Bank Name</th>
                                            <th class="text-left">Account Holder Name</th>
                                            <th class="text-left">Account Number</th>
                                            <th class="text-left">IFSC Code</th>
                                            <th class="text-left">SWIFT Code</th>
                                            <th class="text-left">Branch Name</th>
                                            <th class="text-left">Updated At</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $bank) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($bank->bank_name); ?></td>
                                                <td><?= esc($bank->account_holder); ?></td>
                                                <td><?= esc($bank->account_number); ?></td>
                                                <td><?= esc($bank->ifsc_code); ?></td>
                                                <td><?= esc($bank->swift_code); ?></td>
                                                <td><?= esc($bank->branch_name); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($bank->updated_at)) {
                                                        echo date('j M Y, h:i A', strtotime($bank->updated_at));
                                                    } else {
                                                        echo date('j M Y, h:i A', strtotime($bank->created_at));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $bank->bank_id; ?>" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        <?php } ?>

                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $bank->bank_id; ?>" data-bs-toggle="tooltip" title="Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['totalRecords']) { ?>
                                <br>
                                <?= view('admin/_paging', array('paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray)); ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/banks/create') ?>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        $('#bankDetailsModal').on('hidden.bs.modal', function() {
            $('#bankDetailsForm')[0].reset();
            $('#modalMessages').html('');
            $('#bankId').val('');
            $('#submitButton').text('Save');
            $('#bankDetailsModalLabel').text('Add Bank Details');
        });

        // Handle form submission
        $('#bankDetailsForm').on('submit', function(e) {
            e.preventDefault();
            var url = "<?= site_url('banks/save') ?>";
            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('#modalMessages').html('');

                    if (response.status === 'error') {
                        var errorMessages = '<ul>';
                        if (response.errors) {
                            for (var field in response.errors) {
                                if (response.errors.hasOwnProperty(field)) {
                                    errorMessages += '<li>' + response.errors[field] + '</li>';
                                }
                            }
                        }
                        errorMessages += '</ul>';
                        $('#modalMessages').html(` 
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ${errorMessages}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    } else if (response.status === 'success') {
                        $('#modalMessages').html(` 
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                        setTimeout(function() {
                            $('#bankDetailsModal').modal('hide');
                            location.reload();
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    $('#modalMessages').html(` 
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            An unexpected error occurred. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                }
            });
        });

        // Handle edit button click
        $('.editBtn').on('click', function(e) {
            e.preventDefault();
            var bankId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('banks/edit') ?>/' + bankId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#bankId').val(data.bank_id);
                    $('#bankName').val(data.bank_name);
                    $('#accountHolderName').val(data.account_holder);
                    $('#accountNumber').val(data.account_number);
                    $('#ifscCode').val(data.ifsc_code);
                    $('#swiftCode').val(data.swift_code);
                    $('#branchName').val(data.branch_name);

                    $('#submitButton').text('Update');
                    $('#bankDetailsModalLabel').text('Edit Bank Details');

                    $('#bankDetailsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch color data:', error);
                }
            });
        });

        // Handle delete button click
        $('.dltBtn').click(function(e) {
            e.preventDefault();
            var bankId = $(this).data('id');

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "GET",
                        url: "<?= site_url('banks/delete/') ?>" + bankId,
                        success: function(response) {
                            if (response.status === 'success') {
                                swal(response.message, {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                swal("Error!", response.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Error!", "An unexpected error occurred. Please try again.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>