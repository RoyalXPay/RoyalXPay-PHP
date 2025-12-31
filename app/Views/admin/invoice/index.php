<?php $permissions = session()->get('permissions'); ?>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        /* Initially hide the search form */
    }
</style>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                        <li class="breadcrumb-item active">Invoice List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#newInvoiceModal" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> Add Invoice
                            </button>
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
        <!-- end page title -->

        <div class="search-form">
            <form action="">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label for="txtsearch">Search</label>
                                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Type to search...">
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('invoices'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
                                                    <i class="mdi mdi-refresh"></i> Clear
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                                            <th data-sortable="true" class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Invoice ID</th>
                                            <th data-sortable="true" class="text-center">Company Name</th>
                                            <th data-sortable="true" class="text-center">Product Name</th>
                                            <th data-sortable="true" class="text-center">Amount</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $invoice) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($invoice->invoice_id); ?></td>
                                                <td><?= esc($invoice->company_name); ?></td>
                                                <td><?= esc($invoice->product_name); ?></td>
                                                <td><?= esc($invoice->amount); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <a href="#" class="btn btn-success m-1 editInvoiceBtn" data-id="<?= $invoice->invoice_id ?>" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>

                                                        <a href="<?= site_url("invoices/preview/" . $invoice->invoice_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <form method="post" action="#" style="display: inline;" class="m-1">
                                                                <input type="hidden" id="invoiceId_<?= $invoice->invoice_id; ?>" name="invoiceId" value="<?= $invoice->invoice_id; ?>">
                                                                <button type="button" class="btn btn-danger dltInvoiceBtn" data-id="<?= $invoice->invoice_id; ?>" data-bs-toggle="tooltip" title="Delete">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
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
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div> <!-- End Page-content -->

<?= $this->include('admin/invoice/create') ?>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Check if the search form should be shown
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000; // 10 minutes in milliseconds
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').show();
        } else {
            $('.search-form').hide();
        }

        $('#toggleSearchBtn').click(function() {
            const isVisible = $('.search-form').is(':visible');

            if (isVisible) {
                $('.search-form').hide();
                localStorage.setItem('isSearchFormVisible', 'false');
            } else {
                $('.search-form').show(); // Show the form
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        // Handle invoice creation form submission
        $('#invoiceForm').on('submit', function(e) {
            e.preventDefault();
            var url = "<?= site_url('invoices/save') ?>";

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
                            $('#newInvoiceModal').modal('hide');
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
        $('.editInvoiceBtn').on('click', function(e) {
            e.preventDefault();
            var invoiceId = $(this).data('id');

            // Fetch data via AJAX
            $.ajax({
                url: '<?= site_url('invoices/edit') ?>/' + invoiceId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#invoiceId').val(data.invoice_id);
                    $('#companyName').val(data.company_name);
                    $('#productName').val(data.product_name);
                    $('#amount').val(data.amount);

                    // Update modal title and button text
                    $('#submitButton').text('Update');
                    $('#newInvoiceModalLabel').text('Edit Invoice');

                    // Show the modal
                    $('#newInvoiceModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch invoice data:', error);
                }
            });
        });

        $('.dltInvoiceBtn').click(function(e) {
            e.preventDefault();
            var dataID = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('invoices/delete') ?>",
                        data: {
                            invoiceId: dataID
                        },
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
                } else {
                    swal("Your Data Is Safe!", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>