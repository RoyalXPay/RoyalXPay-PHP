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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Purchases</a></li>
                        <li class="breadcrumb-item active">Purchase List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <a href="<?= site_url('purchases/create') ?>" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> Add Purchase
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

                                            <div class="col-lg-3">
                                                <label for="txtsearch">Search</label>
                                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Type to search...">
                                            </div>

                                            <!-- Product Color -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productColor">Color</label>
                                                <select id="color" class="form-select" name="color">
                                                    <option value="">Select Colour</option>
                                                    <?php foreach ($colors as $color): ?>
                                                        <option value="<?= $color['color_id'] ?>" <?= isset($searchArray['color']) && $searchArray['color'] == $color['color_id'] ? "selected" : ""; ?>>
                                                            <?= $color['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Product Size -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productSize">Size</label>
                                                <select id="sizeInch" class="form-select" name="size">
                                                    <option value="">Select Size</option>
                                                    <?php foreach ($sizes as $size): ?>
                                                        <option value="<?= $size['size_id'] ?>" <?= isset($searchArray['size']) && $searchArray['size'] == $size['size_id'] ? "selected" : ""; ?>>
                                                            <?= $size['size_in_inches'] ?> inches
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Resolution Type -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="resolutionType">Resolution Type</label>
                                                <select id="resolutions" class="form-select" name="resolution_type">
                                                    <option value="">Select Resolution</option>
                                                    <?php foreach ($resolutions as $resolution): ?>
                                                        <option value="<?= $resolution['resolution_id'] ?>" <?= isset($searchArray['resolution_type']) && $searchArray['resolution_type'] == $resolution['resolution_id'] ? "selected" : ""; ?>>
                                                            <?= $resolution['resolution_type'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Touch / Non Touch -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="touchType">Touch / Non Touch</label>
                                                <select id="touchType" class="form-select" name="touch_type">
                                                    <option value="">Select Touch Type</option>
                                                    <?php foreach ($touchTypes as $touchType): ?>
                                                        <option value="<?= $touchType['touch_id'] ?>" <?= isset($searchArray['touch_type']) && $searchArray['touch_type'] == $touchType['touch_id'] ? "selected" : ""; ?>>
                                                            <?= $touchType['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Glass Type -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="glassType">Glass Type</label>
                                                <select id="glassType" class="form-select" name="glass_type">
                                                    <option value="">Select Glass Type</option>
                                                    <?php foreach ($glassTypes as $glassType): ?>
                                                        <option value="<?= $glassType['glass_id'] ?>" <?= isset($searchArray['glass_type']) && $searchArray['glass_type'] == $glassType['glass_id'] ? "selected" : ""; ?>>
                                                            <?= $glassType['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('purchases'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                            <th data-sortable="true" class="text-center">Purchase Id</th>
                                            <th data-sortable="true" class="text-center">Company Name</th>
                                            <th data-sortable="true" class="text-center">Serial Numbers</th>
                                            <th data-sortable="true" class="text-center">Purchase Quantity</th>
                                            <th data-sortable="true" class="text-center">Distribute in Rent</th>
                                            <th data-sortable="true" class="text-center">Distribute in Sale</th>
                                            <th data-sortable="true" class="text-center">Available</th>
                                            <th data-sortable="true" class="text-center">Total Amount</th>
                                            <th data-sortable="true" class="text-center">Payment Status</th>
                                            <th data-sortable="true" class="text-center">Agent Status</th>
                                            <th data-sortable="true" class="text-center">Total CBM</th>
                                            <th data-sortable="true" class="text-center">Purchase Status</th>
                                            <th data-sortable="true" class="text-center">Notes</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $purchase) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($purchase->purchase_id); ?></td>
                                                <td><?= esc($purchase->company_name); ?></td>
                                                <td>
                                                    <?php if (isset($serialNumbers[$purchase->purchase_id]) && !empty($serialNumbers[$purchase->purchase_id])): ?>
                                                        <ul style="list-style-type: disc; padding-left: 15px; margin: 0;">
                                                            <?php foreach ($serialNumbers[$purchase->purchase_id] as $sn): ?>
                                                                <li><?= esc($sn) ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <span style="color: #888;">N/A</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?= isset($totalQuantities[$purchase->purchase_id]) ? $totalQuantities[$purchase->purchase_id] : 0; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php
                                                    $rentQty = 0;
                                                    if (isset($inventorySummaryArray[$purchase->purchase_id])) {
                                                        foreach ($inventorySummaryArray[$purchase->purchase_id] as $summary) {
                                                            $rentQty += $summary['rent'];
                                                        }
                                                    }
                                                    echo $rentQty;
                                                    ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php
                                                    $saleQty = 0;
                                                    if (isset($inventorySummaryArray[$purchase->purchase_id])) {
                                                        foreach ($inventorySummaryArray[$purchase->purchase_id] as $summary) {
                                                            $saleQty += $summary['sale'];
                                                        }
                                                    }
                                                    echo $saleQty;
                                                    ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php
                                                    $availableQty = 0;
                                                    if (isset($inventorySummaryArray[$purchase->purchase_id])) {
                                                        foreach ($inventorySummaryArray[$purchase->purchase_id] as $summary) {
                                                            $availableQty += $summary['available'];
                                                        }
                                                    }
                                                    echo $availableQty;
                                                    ?>
                                                </td>

                                                <td><?= esc($purchase->total_amount); ?></td>
                                                <td><?= formatWords($purchase->payment_status); ?></td>
                                                <td><?= esc($purchase->agent_status); ?></td>
                                                <td><?= esc($purchase->agent_cbm); ?></td>
                                                <td>
                                                    <?php if (isset($purchase->purchase_status)): ?>
                                                        <?php
                                                        $status = strtolower(trim($purchase->purchase_status));
                                                        ?>
                                                        <?php if ($status === 'pending'): ?>
                                                            <span class="badge bg-info"><?= esc($status); ?></span>
                                                        <?php elseif ($status === 'completed'): ?>
                                                            <span class="badge bg-success"><?= esc($status); ?></span>
                                                        <?php elseif ($status === 'stopped'): ?>
                                                            <span class="badge bg-warning"><?= esc($status); ?></span>
                                                        <?php elseif ($status === 'stopped'): ?>
                                                            <span class="badge bg-cancelled"><?= esc($status); ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Pending</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($purchase->notes); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <a href="<?= site_url("purchases/edit/" . $purchase->purchase_id) ?>" class="btn btn-success m-1 editBtn" data-id="<?= $purchase->purchase_id ?>" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>

                                                        <a href="<?= site_url("purchases/preview/" . $purchase->purchase_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <form method="post" action="#" style="display: inline;" class="m-1">
                                                                <input type="hidden" id="purchaseId_<?= $purchase->purchase_id; ?>" name="purchaseId" value="<?= $purchase->purchase_id; ?>">
                                                                <button type="button" class="btn btn-danger dltBtn" data-id="<?= $purchase->purchase_id; ?>" data-bs-toggle="tooltip" title="Delete">
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

        // Search form visibility handling
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
                        type: "POST",
                        url: "<?= site_url('purchases/delete') ?>",
                        data: {
                            purchaseId: dataID
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