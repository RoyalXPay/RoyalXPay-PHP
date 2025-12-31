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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Rent Quotation</a></li>
                        <li class="breadcrumb-item active">Rent Quotation List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <a href="<?= site_url('rent-quotations/create') ?>" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> Add Quotation
                            </a>
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
                                                <a href="<?= site_url('rent-quotations'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                            <th data-sortable="true" class="text-center">Quotation ID</th>
                                            <th data-sortable="true" class="text-center">Customer Name</th>
                                            <th data-sortable="true" class="text-center">Start Date</th>
                                            <th data-sortable="true" class="text-center">End Date</th>
                                            <th data-sortable="true" class="text-center">Product Details</th>
                                            <th data-sortable="true" class="text-center">Final Payable Price</th>
                                            <th data-sortable="true" class="text-center">Created At</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $items) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($items->quotation_code); ?></td>
                                                <td><?= esc($items->customer_name); ?></td>
                                                <td><?= esc($items->start_date); ?></td>
                                                <td><?= esc($items->end_date); ?></td>

                                                <?php
                                                // Initialize variables to hold product information
                                                $productNames = [];
                                                $productPrices = [];
                                                $productQuantities = [];

                                                // Loop through the products related to this quotation
                                                foreach ($quotationProducts as $quotationProductDetails) {
                                                    if ($quotationProductDetails["quotation_id"] == $items->quotation_id) {
                                                        if (!empty($quotationProductDetails["product_id"])) {
                                                            // Find the associated rent product name
                                                            foreach ($rentProducts as $rentProductDetails) {
                                                                if ($quotationProductDetails["product_id"] == $rentProductDetails->product_id) {
                                                                    $productNames[] = $rentProductDetails->product_name;
                                                                }
                                                            }
                                                        }
                                                        $productPrices[] = $quotationProductDetails["price"];
                                                        $productQuantities[] = $quotationProductDetails["quantity"];
                                                    }
                                                }
                                                ?>

                                                <!-- Nested table displaying product details -->
                                                <td>
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Product</th>
                                                                <th class="text-center">Quantity</th>
                                                                <th class="text-center">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php for ($i = 0; $i < count($productNames); $i++) { ?>
                                                                <tr>
                                                                    <td><?= esc($productNames[$i]); ?></td>
                                                                    <td><?= esc($productQuantities[$i]); ?></td>
                                                                    <td><?= esc($productPrices[$i]); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>

                                                <td><?= esc($items->final_payable_price); ?></td>
                                                <td><?= date('j M Y, h:i A', strtotime($items->created_at)); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <!-- Action Buttons -->
                                                            <a href="<?= site_url("rent-quotations/edit/" . $items->quotation_id) ?>" class="btn btn-success m-1" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- Preview Button -->
                                                        <a href="<?= site_url("rent-quotations/preview/" . $items->quotation_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <!-- Generate PDF Button -->
                                                        <a href="<?= site_url("rent-quotations/generate-quotations/" . $items->quotation_id) ?>" class="btn btn-secondary m-1" data-bs-toggle="tooltip" title="Generate Quotation">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <!-- Delete Button -->
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $items->quotation_id; ?>" data-bs-toggle="tooltip" title="Delete">
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
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div> <!-- End Page-content -->

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            "order": [
                [1, "desc"]
            ],
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
                $('.search-form').show();
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        // Handle delete button click
        $('.dltBtn').click(function(e) {
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
                        type: "GET",
                        url: "<?= site_url('rent-quotations/delete/') ?>" + dataID,
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