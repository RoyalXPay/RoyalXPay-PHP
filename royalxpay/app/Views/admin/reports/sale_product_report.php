<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$router = service('router');
$method = $router->methodName();
?>
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                        <li class="breadcrumb-item active"><?= $pageTitle ?></li>
                    </ol>

                    <div class="page-title-right">
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
            <form action="" method="get">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Customer Name / Mobile -->
                                    <div class="col-lg-3">
                                        <label for="customerSearch">Customer Name / Mobile</label>
                                        <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray['txtsearch']) ? $searchArray['txtsearch'] : ''; ?>" placeholder="Search by Name / Mobile">
                                    </div>

                                    <!-- Contract ID -->
                                    <div class="col-lg-3 mt-1">
                                        <label for="rentContractId">Contract ID</label>
                                        <input class="form-control" name="contract_code" type="text" value="<?= isset($searchArray['contract_code']) ? $searchArray['contract_code'] : ''; ?>" placeholder="Enter Contract ID">
                                    </div>

                                    <!-- Quotation ID -->
                                    <div class="col-lg-3 mt-1">
                                        <label for="saleQuotationId">Quotation ID</label>
                                        <input class="form-control" name="quotation_code" type="text" value="<?= isset($searchArray['quotation_code']) ? $searchArray['quotation_code'] : ''; ?>" placeholder="Enter Quotation ID">
                                    </div>

                                    <!-- Product Name -->
                                    <div class="col-lg-3">
                                        <label for="productName">Product Name</label>
                                        <input type="text" class="form-control" name="product_name" value="<?= isset($searchArray['product_name']) ? esc($searchArray['product_name']) : '' ?>" placeholder="Enter product name">
                                    </div>

                                    <!-- Start Date -->
                                    <div class="col-lg-3">
                                        <label for="startDate">Start Date</label>
                                        <input class="form-control" name="start_date" type="date" value="<?= isset($searchArray['start_date']) ? $searchArray['start_date'] : ''; ?>" />
                                    </div>

                                    <!-- End Date -->
                                    <div class="col-lg-3">
                                        <label for="endDate">End Date</label>
                                        <input class="form-control" name="end_date" type="date" value="<?= isset($searchArray['end_date']) ? $searchArray['end_date'] : ''; ?>" />
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

                                    <!-- Submit and Clear Buttons -->
                                    <div class="col-lg-12" style="margin-top: 20px;">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                            Submit
                                        </button>
                                        <a href="<?= site_url('reports/customer-report'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
                                            <i class="mdi mdi-refresh"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="<?= site_url('reports/rent-product'); ?>" role="tab" aria-controls="Rent" aria-selected="true">Rent</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= ($method == 'saleProductReport') ? "active" : "" ?>" href="<?= site_url('reports/sale-product'); ?>" role="tab" aria-controls="Sale" aria-selected="false">Sale</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="rent" role="tabpanel" aria-labelledby="rent-tab">
                <!-- All Users Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <?= view('flash_messages'); ?>
                            <div class="card-body">
                                <?php if ($pagination["totalRecords"] > 0) { ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                            <thead>
                                                <tr>
                                                    <th data-sortable="true" class="text-center">Sl</th>
                                                    <th data-sortable="true" class="text-center">ID</th>
                                                    <th data-sortable="true" class="text-center">Order Type</th>
                                                    <th data-sortable="true" class="text-center">Customer Name</th>
                                                    <th data-sortable="true" class="text-center">Total Price</th>
                                                    <th data-sortable="true" class="text-center">VAT</th>
                                                    <th data-sortable="true" class="text-center">Discount</th>
                                                    <th data-sortable="true" class="text-center">Final Payable Price</th>
                                                    <th data-sortable="true" class="text-center">Date</th>
                                                    <th data-sortable="true" class="text-center">Status</th>
                                                    <th data-sortable="false" class="text-center">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($results as $items) {
                                                    // Determine values based on transaction type
                                                    $transactionCode = ($items['transaction_type'] == "quotation") ? $items['quotation_code'] : $items['contract_code'];
                                                    $priceOrAmount = ($items['transaction_type'] == "quotation") ? $items['total_price'] : $items['total_amount'];
                                                    $finalPayable = ($items['transaction_type'] == "quotation") ? $items['final_payable_price'] : $items['final_payable_amount'];
                                                    $transactionDate = ($items['transaction_type'] == "quotation") ? $items['quotation_date'] : $items['contract_date'];

                                                    if ($items['transaction_type'] == 'quotation') {
                                                        $redirectUrl = "sale-quotations/preview/" . esc($items['quotation_id']);
                                                    } else {
                                                        $redirectUrl = "sale-contracts/preview/" . esc($items['contract_id']);
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= ++$startLimit; ?></td>
                                                        <td><?= esc($transactionCode); ?></td>
                                                        <td><?= esc($items['transaction_type']); ?></td>
                                                        <td><?= esc($items['customer_name']); ?></td>
                                                        <td><?= esc($priceOrAmount); ?></td>
                                                        <td><?= number_format($items['vat'], 2); ?></td>
                                                        <td><?= number_format($items['discount'], 2); ?></td>
                                                        <td><?= esc($finalPayable); ?></td>
                                                        <td><?= esc($transactionDate); ?></td>
                                                        <td><?= esc($items['status']); ?></td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <a href="<?= site_url($redirectUrl) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
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
            </div>
        </div> <!-- end tab-content -->
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
    });
</script>

<?= $this->endSection() ?>