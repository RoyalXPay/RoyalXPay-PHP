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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Orders</a></li>
                        <li class="breadcrumb-item active">Order List</li>
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
            <form action="">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <div class="col-lg-3">
                                                <label for="txtsearch">Product Name / Price</label>
                                                <input class="form-control" id="txtsearch" name="txtsearch" type="text" value="<?= isset($searchArray['txtsearch']) ? $searchArray['txtsearch'] : ''; ?>" placeholder="Search by Name / Price">
                                            </div>

                                            <!-- Product Color -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productColor">Color</label>
                                                <select id="productColor" class="form-select" name="product_color">
                                                    <option value="">Select Colour</option>
                                                    <?php foreach ($colors as $color): ?>
                                                        <option value="<?= $color['color_id'] ?>" <?= isset($searchArray['product_color']) && $searchArray['product_color'] == $color['color_id'] ? "selected" : ""; ?>>
                                                            <?= $color['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Product Size -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productSize">Size</label>
                                                <select id="productSize" class="form-select" name="product_size">
                                                    <option value="">Select Size</option>
                                                    <?php foreach ($sizes as $size): ?>
                                                        <option value="<?= $size['size_id'] ?>" <?= isset($searchArray['product_size']) && $searchArray['product_size'] == $size['size_id'] ? "selected" : ""; ?>>
                                                            <?= $size['size_in_inches'] ?> inches
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Resolution Type -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productResolutionType">Resolution Type</label>
                                                <select id="productResolutionType" class="form-select" name="product_resolution_type">
                                                    <option value="">Select Resolution</option>
                                                    <?php foreach ($resolutions as $resolution): ?>
                                                        <option value="<?= $resolution['resolution_id'] ?>" <?= isset($searchArray['product_resolution_type']) && $searchArray['product_resolution_type'] == $resolution['resolution_id'] ? "selected" : ""; ?>>
                                                            <?= $resolution['resolution_type'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Touch / Non Touch -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productTouchType">Touch / Non Touch</label>
                                                <select id="productTouchType" class="form-select" name="product_touch_type">
                                                    <option value="">Select Touch Type</option>
                                                    <?php foreach ($touchTypes as $touchType): ?>
                                                        <option value="<?= $touchType['touch_id'] ?>" <?= isset($searchArray['product_touch_type']) && $searchArray['product_touch_type'] == $touchType['touch_id'] ? "selected" : ""; ?>>
                                                            <?= $touchType['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Glass Type -->
                                            <div class="col-lg-3 mt-1">
                                                <label for="productGlassType">Glass Type</label>
                                                <select id="productGlassType" class="form-select" name="product_glass_type">
                                                    <option value="">Select Glass Type</option>
                                                    <?php foreach ($glassTypes as $glassType): ?>
                                                        <option value="<?= $glassType['glass_id'] ?>" <?= isset($searchArray['product_glass_type']) && $searchArray['product_glass_type'] == $glassType['glass_id'] ? "selected" : ""; ?>>
                                                            <?= $glassType['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('orders'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                            <th class="text-left">Order #</th>
                                            <th class="text-left">Customer</th>
                                            <th class="text-left">Company Name</th>
                                            <th class="text-left">Date</th>
                                            <th class="text-left">Total</th>
                                            <th class="text-left">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $order) {
                                        ?>
                                            <tr>
                                                <td>#<?= $order->order_id; ?></td>
                                                <td><?= esc($order->name ?? 'Guest'); ?></td>
                                                <td><?= esc($order->company ?? 'Guest'); ?></td>
                                                <td><?= date('M d, Y', strtotime($order->created_at)); ?></td>
                                                <td>$<?= number_format($order->total, 2); ?></td>
                                                <td>
                                                    <span class="order-status status-<?= $order->status ?>">
                                                        <?= ucfirst($order->status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <a href="#" class="btn btn-success m-1 editBtn" data-id="<?= $order->order_id ?>" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <a href="<?= site_url("orders/preview/" . $order->order_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $order->order_id; ?>" data-bs-toggle="tooltip" title="Delete">
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