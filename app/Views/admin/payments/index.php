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

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Contracts</a></li>
                        <li class="breadcrumb-item active">Payment History</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#newPaymentHistoryModal" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i>Add New Payment
                            </button>
                        <?php } ?>

                        <a href="<?= site_url("generate-invoice/" . $contractDetails['contract_id']) ?>" class="btn btn-secondary m-1" data-bs-toggle="tooltip" title="Generate Invoice">
                            <i class="fas fa-file-pdf"></i> Generate Invoice
                        </a>

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
                                                <a href="<?= site_url("contract-payment-history?contract_id=" . $_GET["contract_id"]) ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                <table id="datatable" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Contract Code</th>
                                            <th data-sortable="true" class="text-center">Paid Amount</th>
                                            <th data-sortable="true" class="text-center">Balance Amount</th>
                                            <th data-sortable="true" class="text-center">Payment Status</th>
                                            <th data-sortable="true" class="text-center">Notes</th>
                                            <th data-sortable="true" class="text-center">Payment Date</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $totalPaymentAmount = 0;
                                        foreach ($results as $paymentHistory) {
                                            $totalPaymentAmount += $paymentHistory->payment_amount;
                                        ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($paymentHistory->contract_code); ?></td>
                                                <td><?= number_format((float)$paymentHistory->payment_amount, 3); ?></td>
                                                <td><?= number_format((float)$paymentHistory->balance_amount, 3); ?></td>
                                                <td>
                                                    <?php
                                                    // Assign Bootstrap badge class based on the payment status
                                                    $status = $paymentHistory->payment_status;
                                                    $badgeClass = '';

                                                    if ($status == 'Pending') {
                                                        $badgeClass = 'bg-primary';
                                                    } elseif ($status == 'Partially Paid') {
                                                        $badgeClass = 'bg-warning';
                                                    } elseif ($status == 'Completed') {
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($status == 'Rejected') {
                                                        $badgeClass = 'bg-danger';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass; ?>"><?= $paymentHistory->payment_status; ?></span>
                                                </td>
                                                <td><?= esc($paymentHistory->notes); ?></td>
                                                <td>
                                                    <!-- Format the payment_date -->
                                                    <?php
                                                    if (!empty($paymentHistory->payment_date)) {
                                                        echo date('j M Y, h:i A', strtotime($paymentHistory->payment_date));
                                                    } else {
                                                        echo date('j M Y, h:i A', strtotime($paymentHistory->created_at));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <!-- Edit Payment Button -->
                                                            <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $paymentHistory->payment_id; ?>" data-bs-toggle="tooltip" title="Edit Payment">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        <?php } ?>
                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <!-- Delete Button -->
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $paymentHistory->payment_id; ?>" data-bs-toggle="tooltip" title="Delete Payment">
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

        <!-- Contract Information Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Contract Payment Summary</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Contract ID:</strong> <?= $contractDetails['contract_code']; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Final Amount:</strong>
                                <?= isset($contractDetails['final_payable_amount']) ? $contractDetails['final_payable_amount'] : 0 ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Paid Amount:</strong>
                                <?= number_format($totalPaymentAmount ?? 0, 3); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Balance Amount:</strong>
                                <?= number_format($lastPaymentHistory->balance_amount ?? 0, 3); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Contract Information Section -->
    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->
<?php
$finalPayableAmount = 0;
if (empty($lastPaymentHistory)) {
    $finalPayableAmount = $contractDetails['final_payable_amount'];
} else {
    $finalPayableAmount = $lastPaymentHistory->final_amount;
}
?>
<!-- Start Modal -->
<div class="modal fade" id="newPaymentHistoryModal" tabindex="-1" aria-labelledby="newPaymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newPaymentHistoryModalLabel">Add Payment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="paymentHistoryForm" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="paymentId" name="payment_id">
                    <input type="hidden" id="contractId" name="contract_id" value="<?= isset($contractDetails['contract_id']) ? $contractDetails['contract_id'] : ''; ?>">

                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label for="paymentDate" class="form-label">Payment Date<span class="mandatory-field">*</span></label>
                        <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                    </div>

                    <!-- Final Amount -->
                    <div class="mb-3">
                        <label for="finalAmount" class="form-label">Final Amount<span class="mandatory-field">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="finalAmount" name="final_amount" required placeholder="Enter final amount" readonly>
                    </div>

                    <!-- Payment Amount -->
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Payment Amount<span class="mandatory-field">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="paymentAmount" name="payment_amount" required placeholder="Enter payment amount">
                    </div>

                    <!-- Balance Amount -->
                    <div class="mb-3">
                        <label for="balanceAmount" class="form-label">Balance Amount<span class="mandatory-field">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="balanceAmount" name="balance_amount" required placeholder="Enter balance amount">
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method<span class="mandatory-field">*</span></label>
                        <select class="form-select" name="payment_method" id="paymentMethod" required>
                            <option value="">Select Payment Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Online">Online</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- Cheque Fields -->
                    <div id="chequeFields" style="display: none;">
                        <div class="mb-3">
                            <label for="chequeDate" class="form-label">Cheque Date</label>
                            <input type="date" class="form-control" id="chequeDate" name="cheque_date">
                        </div>
                        <div class="mb-3">
                            <label for="chequeNumber" class="form-label">Cheque Number</label>
                            <input type="text" class="form-control" id="chequeNumber" name="cheque_number" placeholder="Enter cheque number">
                        </div>
                        <div class="mb-3">
                            <label for="bankName" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="bank_name" placeholder="Enter bank name">
                        </div>
                    </div>

                    <!-- Online Transaction Field -->
                    <div id="onlineFields" style="display: none;">
                        <div class="mb-3">
                            <label for="transactionId" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" id="transactionId" name="transaction_id" placeholder="Enter transaction ID">
                        </div>
                    </div>

                    <!-- Upload Receipt -->
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Upload Receipt</label>
                        <input type="file" class="form-control" id="receipt" name="receipt">

                        <!-- Display existing file download link if file exists -->
                        <div id="existingFile" class="mt-2" style="display: none;">
                            <a href="" id="downloadLink" target="_blank" class="btn btn-link">
                                <i class="fas fa-download"></i> Download Receipt
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="paymentStatus" class="form-label">Payment Status</label>
                        <select class="form-select" name="payment_status" id="paymentStatus">
                            <option value="">Select Payment Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Partially Paid">Partially Paid</option>
                            <option value="Completed">Completed</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Enter any additional notes"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end Modal -->
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        var today = new Date().toISOString().split('T')[0];

        // Set only if the input fields are empty
        if (!$('#paymentDate').val()) {
            $('#paymentDate').val(today);
        }

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
                $('.search-form').show();
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        $('#paymentMethod').change(function() {
            var paymentMethod = $(this).val();

            // Hide all conditional fields initially
            $('#chequeFields').hide();
            $('#onlineFields').hide();

            if (paymentMethod === 'Cheque') {
                $('#chequeFields').show();
            } else if (paymentMethod === 'Online') {
                $('#onlineFields').show();
            }
        });

        const finalPayableAmount = <?= $finalPayableAmount; ?>;
        $('#finalAmount').val(finalPayableAmount.toFixed(3));

        $('#paymentAmount').on('input', function() {
            const paymentAmount = parseFloat($(this).val()) || 0;
            const finalAmount = parseFloat($('#finalAmount').val()) || 0;
            const balanceAmount = finalAmount - paymentAmount;
            $('#balanceAmount').val(balanceAmount.toFixed(3));
        });

        $('#paymentAmount, #balanceAmount').on('blur', function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                $(this).val(val.toFixed(3));
            } else {
                $(this).val('');
            }
        });

        $('#newPaymentHistoryModal').on('hidden.bs.modal', function() {
            // Reset the form
            $('#paymentHistoryForm')[0].reset();

            // Clear any messages
            $('#modalMessages').html('');

            // Reset the final amount to the original value
            $('#finalAmount').val(finalPayableAmount);

            // Reset balance amount
            $('#balanceAmount').val('');

            // Hide conditional fields
            $('#chequeFields, #onlineFields').hide();

            // Reset file input and hide existing file display
            $('#receipt').val('');
            $('#existingFile').hide();

            // Reset modal title and button text to default
            $('#submitButton').text('Submit');
            $('#newPaymentHistoryModalLabel').text('New Payment History');

            // Clear any hidden fields that might have been set
            $('#paymentId').val('');

            // Reset payment date to today if empty
            if (!$('#paymentDate').val()) {
                $('#paymentDate').val(new Date().toISOString().split('T')[0]);
            }

            // Reset payment method dropdown
            $('#paymentMethod').val('').trigger('change');
        });

        // Handle form submission
        $('#paymentHistoryForm').on('submit', function(e) {
            e.preventDefault();

            var url = "<?= site_url('contract-payment-history/save') ?>";
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
                            // Loop through the errors and display them
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
                            $('#newPaymentHistoryModal').modal('hide');
                            location.reload();
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors that occur during the AJAX request
                    $('#modalMessages').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            An unexpected error occurred. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                }
            });
        });

        // Handle edit button click for Payment History
        $('.editBtn').on('click', function(e) {
            e.preventDefault();
            var paymentId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('contract-payment-history/edit') ?>/' + paymentId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate the modal fields with the returned data
                    $('#paymentId').val(data.payment_id);
                    $('#contractId').val(data.contract_id);
                    $('#paymentDate').val(data.payment_date);

                    const paymentAmount = parseFloat(data.payment_amount) || 0;
                    const balanceAmount = parseFloat(data.balance_amount) || 0;
                    const finalAmount = parseFloat(data.final_amount) || 0;

                    $('#paymentAmount').val(paymentAmount.toFixed(3));
                    $('#balanceAmount').val(balanceAmount.toFixed(3));
                    $('#finalAmount').val((finalAmount + paymentAmount).toFixed(3));
                    $('#notes').val(data.notes);

                    // Handle payment method
                    $('#paymentMethod').val(data.payment_method).trigger('change');

                    // Handle cheque fields
                    if (data.payment_method === 'Cheque') {
                        $('#chequeDate').val(data.cheque_date);
                        $('#chequeNumber').val(data.cheque_number);
                        $('#bankName').val(data.bank_name);

                        $('#chequeFields').show();
                        $('onlineFields').hide();
                    }

                    // Handle online fields
                    if (data.payment_method === 'Online') {
                        $('#transactionId').val(data.transaction_id);

                        $('onlineFields').show();
                        $('#chequeFields').hide();
                    }

                    // Hide cheque and online fields if payment method is not cheque or online
                    if (data.payment_method !== 'Cheque' && data.payment_method !== 'Online') {
                        $('#chequeFields, #onlineFields').hide();
                    }

                    // Handle receipt file
                    if (data.receipt) {
                        $('#existingFile').show();
                        $('#downloadLink').attr('href', '<?= base_url('uploads/payment_receipts/') ?>' + data.receipt);
                    } else {
                        $('#existingFile').hide();
                    }

                    // Update modal title and button text for editing
                    $('#submitButton').text('Update Payment');
                    $('#newPaymentHistoryModalLabel').text('Edit Payment History');

                    // Show the modal
                    $('#newPaymentHistoryModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch payment data:', error);
                }
            });
        });

        $('.dltBtn').on('click', function(e) {
            e.preventDefault();
            const paymentId = $(this).data('id');
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
                        url: "<?= site_url('contract-payment-history/delete/') ?>" + paymentId,
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