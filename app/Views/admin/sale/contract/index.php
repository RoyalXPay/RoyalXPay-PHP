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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Contracts</a></li>
                        <li class="breadcrumb-item active">Sale Contract List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <a href="<?= site_url('sale-contracts/create') ?>" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> Add Sale Contract
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
                                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search Contract...">
                                            </div>

                                            <div class="col-lg-3">
                                                <label for="searchContractNumber">Contract Number</label>
                                                <input class="form-control" name="contract_number" type="text" value="<?= isset($searchArray['contract_number']) ? $searchArray['contract_number'] : ''; ?>" placeholder="Contract Number">
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('sale-contracts'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                <table id="datatable1" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Contract ID</th>
                                            <th data-sortable="true" class="text-center">Customer Name</th>
                                            <th data-sortable="true" class="text-center">Products Details</th>
                                            <th data-sortable="true" class="text-center">Final Payable Price</th>
                                            <th data-sortable="true" class="text-center">Balance Amount</th>
                                            <th data-sortable="true" class="text-center">Delivery Status</th>
                                            <th data-sortable="true" class="text-center">Payment Status</th>
                                            <th data-sortable="true" class="text-center">Contract Status</th>
                                            <th data-sortable="true" class="text-center">Created At</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $items) {

                                            // Set background color based on payment status
                                            $paymentStatus = "";
                                            if (isset($items->payment_status)) {
                                                $paymentStatus = strtolower(trim($items->payment_status));
                                            }
                                            if ($paymentStatus == 'pending') {
                                                $bgColor = '#D6EAF8';
                                            } elseif ($paymentStatus == 'partially paid') {
                                                $bgColor = '#F6DDCC';
                                            } elseif ($paymentStatus == 'completed') {
                                                $bgColor = '#D5F5E3';
                                            } elseif ($paymentStatus == 'rejected') {
                                                $bgColor = '#FADBD8';
                                            } else {
                                                $bgColor = '#D6EAF8';
                                                $paymentStatus = "pending";
                                            }

                                            $deliveryStatus = "";
                                            if (isset($items->delivery_status)) {
                                                $deliveryStatus = strtolower(trim($items->delivery_status));
                                            }
                                            if ($deliveryStatus == 'pending') {
                                                $deliveryBgColor = '#D6EAF8';
                                            } elseif ($deliveryStatus == 'partially delivered') {
                                                $deliveryBgColor = '#F6DDCC';
                                            } elseif ($deliveryStatus == 'delivered') {
                                                $deliveryBgColor = '#D5F5E3';
                                            } elseif ($deliveryStatus == 'rejected') {
                                                $deliveryBgColor = '#FADBD8';
                                            } else {
                                                $deliveryBgColor = '#D6EAF8';
                                                $deliveryStatus = "pending";
                                            }
                                        ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($items->contract_code); ?></td>
                                                <td><?= esc($items->customer_name); ?></td>
                                                <?php
                                                // Initialize variables to hold product information
                                                $productNames = [];
                                                $productPrices = [];
                                                $productQuantities = [];

                                                foreach ($contractProducts as $contractProductDetails) {
                                                    if ($contractProductDetails["contract_id"] == $items->contract_id) {
                                                        if (!empty($contractProductDetails["product_master_id"])) {
                                                            foreach ($products as $product) {
                                                                if ($contractProductDetails["product_master_id"] == $product->product_master_id) {
                                                                    $productNames[] = $product->product_name;
                                                                }
                                                            }
                                                        } elseif (!empty($contractProductDetails["product_id"])) {
                                                            foreach ($saleProducts as $saleProduct) {
                                                                if ($contractProductDetails["product_id"] == $saleProduct->product_id) {
                                                                    $productNames[] = $saleProduct->product_name;
                                                                }
                                                            }
                                                        }
                                                        $productPrices[] = $contractProductDetails["price"];
                                                        $productQuantities[] = $contractProductDetails["quantity"];
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
                                                                    <td class="text-center"><?= esc($productNames[$i]); ?></td>
                                                                    <td class="text-center"><?= esc($productQuantities[$i]); ?></td>
                                                                    <td class="text-center"><?= esc($productPrices[$i]); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>

                                                <td><?= esc($items->final_payable_amount); ?></td>
                                                <td>
                                                    <?php
                                                    $contractId = $items->contract_id;

                                                    if (!isset($totalBalanceAmount[$contractId])) {
                                                        // Contract ID not found, show the final payable amount
                                                        echo esc($items->final_payable_amount);
                                                    } elseif ($totalBalanceAmount[$contractId] == 0) {
                                                        // Contract ID found with balance 0, show "Fully Paid"
                                                        echo '<span class="text-success font-weight-bold">Fully Paid</span>';
                                                    } else {
                                                        // Display the balance amount
                                                        echo esc($totalBalanceAmount[$contractId]);
                                                    }
                                                    ?>
                                                </td>
                                                <td style="background-color: <?= $deliveryBgColor; ?>;">
                                                    <?= formatWords($deliveryStatus); ?>
                                                </td>
                                                <td style="background-color: <?= $bgColor; ?>;">
                                                    <?= formatWords($paymentStatus); ?>
                                                </td>
                                                <td><?= esc($items->status); ?></td>
                                                <td><?= date('j M Y, h:i A', strtotime($items->created_at)); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <button type="button" class="btn btn-primary m-1 toggle-actions-btn" style="width: 45px;">
                                                            <i class="fas fa-cogs"></i>
                                                        </button>

                                                        <div class="action-buttons d-none">
                                                            <a href="<?= site_url("contract-payment-history?contract_id=" . $items->contract_id) ?>" class="btn btn-info m-1" data-bs-toggle="tooltip" title="Contract Payment Info">
                                                                <i class="fas fa-credit-card"></i>
                                                            </a>
                                                            <a href="<?= site_url("sale-contracts/generate-pdf/" . $items->contract_id) ?>" class="btn btn-secondary m-1" data-bs-toggle="tooltip" title="Generate Contract">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                            <a href="<?= site_url("sale-contracts/generate-invoice/" . $items->contract_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Generate Invoice">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                            <a href="<?= site_url("contract-delivery-history?contract_id=" . $items->contract_id) ?>" class="btn btn-primary m-1" data-bs-toggle="tooltip" title="Contract Delivery Info">
                                                                <i class="fas fa-truck"></i>
                                                            </a>
                                                            <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                                <a href="<?= site_url("sale-contracts/edit/" . $items->contract_id) ?>" class="btn btn-success m-1" data-bs-toggle="tooltip" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            <?php } ?>
                                                            <a href="<?= site_url("sale-contracts/preview/" . $items->contract_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                                <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $items->contract_id; ?>" data-bs-toggle="tooltip" title="Delete">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            <?php } ?>
                                                        </div>
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
        $('#datatable1').DataTable({
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

        $('.toggle-actions-btn').on('click', function() {
            // Find the closest row's action buttons div and toggle its visibility
            $(this).siblings('.action-buttons').toggleClass('d-none');
        });
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
                        url: "<?= site_url('sale-contracts/delete/') ?>" + dataID,
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