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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
                        <li class="breadcrumb-item active">Rent Products List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <a href="<?= site_url('rent-products/create') ?>" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> Add Product
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
                                                <label for="txtsearch">Product Name / Price</label>
                                                <input class="form-control" id="txtsearch" name="txtsearch" type="text" value="<?= isset($searchArray['txtsearch']) ? $searchArray['txtsearch'] : ''; ?>" placeholder="Search by Name / Price">
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
                                                <a href="<?= site_url('rent-products'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                <table id="datatable1" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Product Id</th>
                                            <th data-sortable="true" class="text-center">Product Name</th>
                                            <th data-sortable="true" class="text-center">Serial Number</th>
                                            <th data-sortable="true" class="text-center">Size</th>
                                            <th data-sortable="true" class="text-center">Colour</th>
                                            <th data-sortable="true" class="text-center">Glass Type</th>
                                            <th data-sortable="true" class="text-center">Touch/ Non Touch</th>
                                            <th data-sortable="true" class="text-center">Resolution Type</th>
                                            <th data-sortable="true" class="text-center">Quantity</th>
                                            <th data-sortable="true" class="text-center">Price Per day</th>
                                            <th data-sortable="false" class="text-center">Image</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $items) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($items->product_id); ?></td>
                                                <td><?= esc($items->product_name); ?></td>
                                                <td><?= esc($items->serial_number ?? 'N/A'); ?></td>
                                                <td><?= esc($items->size_in_inches); ?></td>
                                                <td><?= esc($items->color_name); ?></td>
                                                <td><?= esc($items->glass_name); ?></td>
                                                <td><?= esc($items->touch_name); ?></td>
                                                <td><?= esc($items->resolution_type); ?></td>
                                                <td><?= esc($items->quantity); ?></td>
                                                <td><?= esc($items->price_per_day); ?></td>
                                                <td class="text-center">
                                                    <?php
                                                    $images = explode(',', $items->product_images);
                                                    $imageUrl = base_url('uploads/products/' . $images[0]);
                                                    ?>
                                                    <img src="<?= $imageUrl ?>" alt="<?= $items->product_name ?>" class="img-fluid" style="max-height: 100px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <a href="<?= site_url("rent-products/edit/" . $items->product_id) ?>" class="btn btn-success m-1 editBtn" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>

                                                        <a href="<?= site_url("rent-products/preview/" . $items->product_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $items->product_id; ?>" data-bs-toggle="tooltip" title="Delete">
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
        $('#datatable1').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        // Search form visibility based on localStorage
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

        // Delete product button
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
                        url: "<?= site_url('rent-products/delete/') ?>" + dataID,
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