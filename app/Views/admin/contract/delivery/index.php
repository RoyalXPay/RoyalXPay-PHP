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
                        <li class="breadcrumb-item active">Delivery History</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#newDeliveryModal" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i>Add New Delivery
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
                                                <a href="<?= site_url("contract-delivery-history?contract_id=" . $_GET["contract_id"]) ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                            <th data-sortable="true" class="text-center">Contract Code</th>
                                            <th data-sortable="true" class="text-center">Delivery Date</th>
                                            <th data-sortable="true" class="text-center">Delivery Agent</th>
                                            <th data-sortable="true" class="text-center">Delivery Status</th>
                                            <th data-sortable="true" class="text-center">Notes</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $deliveryHistory) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($deliveryHistory->contract_code); ?></td>
                                                <td>
                                                    <?= date('j M Y', strtotime($deliveryHistory->delivery_date)); ?>
                                                </td>
                                                <td><?= esc($deliveryHistory->delivery_agent); ?></td>
                                                <td>
                                                    <?php
                                                    // Assign Bootstrap badge class based on the payment status
                                                    $status = $deliveryHistory->delivery_status;
                                                    $badgeClass = '';

                                                    if ($status == 'Pending') {
                                                        $badgeClass = 'bg-primary';
                                                    } elseif ($status == 'Partially Delivered') {
                                                        $badgeClass = 'bg-warning';
                                                    } elseif ($status == 'Delivered') {
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($status == 'Rejected') {
                                                        $badgeClass = 'bg-danger';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass; ?>"><?= $deliveryHistory->delivery_status; ?></span>
                                                </td>
                                                <td><?= esc($deliveryHistory->notes); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <!-- Edit Delivery Button -->
                                                            <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $deliveryHistory->delivery_id; ?>" data-bs-toggle="tooltip" title="Edit Delivery">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        <?php } ?>
                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <!-- Delete Button -->
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $deliveryHistory->delivery_id; ?>" data-bs-toggle="tooltip" title="Delete Delivery">
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
        <!-- end row -->
    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<!-- Start Modal -->
<div class="modal fade" id="newDeliveryModal" tabindex="-1" aria-labelledby="newDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newDeliveryModalLabel">Add Delivery History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="deliveryHistoryForm" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="deliveryId" name="delivery_id">
                    <input type="hidden" id="contractId" name="contract_id" value="<?= isset($_GET['contract_id']) ? $_GET['contract_id'] : ''; ?>">

                    <!-- Delivery Date -->
                    <div class="mb-3">
                        <label for="deliveryDate" class="form-label">Delivery Date<span class="mandatory-field">*</span></label>
                        <input type="date" class="form-control" id="deliveryDate" name="delivery_date" required>
                    </div>

                    <!-- Delivery Agent -->
                    <div class="mb-3">
                        <label for="deliveryAgent" class="form-label">Delivery Agent</label>
                        <input type="text" class="form-control" id="deliveryAgent" name="delivery_agent" placeholder="Enter delivery agent name">
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="image" name="image">

                        <!-- Display existing file download link if file exists -->
                        <div id="existingFile" class="mt-2" style="display: none;">
                            <a href="" id="downloadLink" target="_blank" class="btn btn-link">
                                <i class="fas fa-download"></i> Download Image
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deliveryStatus" class="form-label">Delivery Status</label>
                        <select class="form-select" name="delivery_status" id="deliveryStatus">
                            <option value="">Select Delivery Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Partially Delivered">Partially Delivered</option>
                            <option value="Delivered">Delivered</option>
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
            <!-- end modal body -->
        </div>
        <!-- end modal-content -->
    </div>
    <!-- end modal-dialog -->
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
        if (!$('#deliveryDate').val()) {
            $('#deliveryDate').val(today);
        }
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Handle form submission
        $('#deliveryHistoryForm').on('submit', function(e) {
            e.preventDefault();

            var url = "<?= site_url('contract-delivery-history/save') ?>";
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
                            $('#newDeliveryModal').modal('hide');
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

        // Handle edit button click for Delivery History
        $('.editBtn').on('click', function(e) {
            e.preventDefault();
            var deliveryId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('contract-delivery-history/edit') ?>/' + deliveryId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#deliveryId').val(data.delivery_id);
                    $('#contractId').val(data.contract_id);
                    $('#deliveryDate').val(data.delivery_date);
                    $('#deliveryAgent').val(data.delivery_agent);
                    $('#deliveryStatus').val(data.delivery_status);
                    $('#notes').val(data.notes);
                    // Check if there is an existing file
                    if (data.image) {
                        $('#existingFile').show();
                        $('#downloadLink').attr('href', '<?= base_url('uploads/contract/delivery/') ?>' + data.image);
                    } else {
                        $('#existingFile').hide();
                    }
                    // Change the modal title and button text to indicate it's an edit
                    $('#submitButton').text('Update Delivery');
                    $('#newDeliveryModalLabel').text('Edit Delivery History');

                    // Show the modal
                    $('#newDeliveryModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch delivery data:', error);
                }
            });
        });

        // Handle delete button click for Delivery History
        $('.dltBtn').on('click', function(e) {
            e.preventDefault();
            const deliveryId = $(this).data('id');
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
                        url: "<?= site_url('contract-delivery-history/delete/') ?>" + deliveryId,
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