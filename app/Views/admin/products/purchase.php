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
                        <li class="breadcrumb-item active">Products List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($permissions) && in_array('add', $permissions)) { ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#newProductModal" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i>Add Product
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

                                            <div class="col-lg-3">
                                                <label for="searchProductType">Product Type</label>
                                                <select id="searchProductType" class="form-control" name="product_type" required>
                                                    <option value="">Select Product Type</option>
                                                    <option value="Rent" <?= (isset($searchArray['product_type']) &&  $searchArray['product_type'] == 'Rent') ? "selected" : ""; ?>>Rent</option>
                                                    <option value="Sale" <?= (isset($searchArray['product_type']) &&  $searchArray['product_type'] == 'Sale') ? "selected" : ""; ?>>Sale</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('products'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                            <th data-sortable="true" class="text-center">Product Id</th>
                                            <th data-sortable="true" class="text-center">Product Name</th>
                                            <th data-sortable="true" class="text-center">Size</th>
                                            <th data-sortable="true" class="text-center">Colour</th>
                                            <th data-sortable="true" class="text-center">Glass Type</th>
                                            <th data-sortable="true" class="text-center">Touch/ Non Touch</th>
                                            <th data-sortable="true" class="text-center">Resolution Type</th>
                                            <th data-sortable="true" class="text-center">Final Price</th>
                                            <th data-sortable="false" class="text-center">Image</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $items) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td class="text-center"><?= esc($items->product_master_id); ?></td>
                                                <td><?= esc($items->product_name); ?></td>
                                                <td><?= esc($items->size_in_inches); ?></td>
                                                <td><?= esc($items->color_name); ?></td>
                                                <td><?= esc($items->glass_name); ?></td>
                                                <td><?= esc($items->resolution_type); ?></td>
                                                <td><?= esc($items->touch_name); ?></td>
                                                <td><?= number_format($items->final_product_price, 2); ?></td>
                                                <td>
                                                    <?php
                                                    $images = explode(',', $items->product_images);
                                                    $imageUrl = base_url('uploads/products/' . $images[0]);
                                                    ?>
                                                    <div class="popup-gallery d-flex flex-wrap">
                                                        <a href="<?= $imageUrl ?>" title="<?= $items->product_name ?>">
                                                            <div class="img-fluid">
                                                                <img src="<?= $imageUrl ?>" alt="<?= $items->product_name ?>" width="120" />
                                                            </div>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($permissions) && in_array('edit', $permissions)) { ?>
                                                            <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $items->product_master_id; ?>" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        <?php } ?>
                                                        <a href="<?= site_url("product-master/preview/" . $items->product_master_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if (!empty($permissions) && in_array('delete', $permissions)) { ?>
                                                            <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $items->product_master_id; ?>" data-bs-toggle="tooltip" title="Delete">
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
<?= $this->include('admin/products/create') ?>

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
                $('.search-form').show();
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        $('#newProductModal').on('hidden.bs.modal', function() {
            $('#productForm')[0].reset();
            $('#modalMessages').html('');
            $('#productId').val('');
            $('#productImages').val('');
            $('#submitButton').text('Save');
            $('#newProductModalLabel').text('Add/Edit Product');

            // Clear the image previews
            const imgPreviews = document.getElementById('imagePreviews');
            imgPreviews.innerHTML = ''; // Clear existing image previews
        });

        // Function to format number with 3 decimal places
        function formatToThreeDecimals(value) {
            return parseFloat(value).toFixed(3);
        }

        document.getElementById('productPriceSale').addEventListener('blur', function() {
            this.value = formatToThreeDecimals(this.value);
            updateFinalPrice();
        });

        document.getElementById('discountPercentage').addEventListener('blur', function() {
            updateFinalPrice();
        });

        function updateFinalPrice() {
            const price = parseFloat(document.getElementById('productPriceSale').value) || 0;
            const discount = parseFloat(document.getElementById('discountPercentage').value) || 0;
            const finalPrice = price - (price * (discount / 100));
            document.getElementById('finalProductPrice').value = formatToThreeDecimals(finalPrice);
        }

        // Handle form submission
        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            var url = $('#productId').val() ? "<?= site_url('product-master/update') ?>" : "<?= site_url('product-master/create') ?>";

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
                            $('#newProductModal').modal('hide');
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

        $('.editBtn').on('click', function(e) {
            e.preventDefault();
            var productTypeId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('product-master/edit') ?>/' + productTypeId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#productId').val(data.product_master_id);
                    $('#productName').val(data.product_name);
                    $('#serialNumber').val(data.serial_number);
                    $('#productDescription').val(data.description);
                    $('#productPriceSale').val(data.product_price);
                    $('#discountPercentage').val(data.discount_percentage);
                    $('#finalProductPrice').val(data.final_product_price);
                    $('#sizeInch').val(data.size_id);
                    $('#color').val(data.color_id);
                    $('#touchType').val(data.touch_id);
                    $('#glassType').val(data.glass_id);
                    $('#resolutions').val(data.resolution_id);
                    $('#productQuantity').val(data.quantity);
                    $('#additionalInformation').val(data.additional_information);

                    // Update modal title and button text
                    $('#submitButton').text('Update');
                    $('#newProductModalLabel').text('Edit Product');

                    // Show existing images
                    const imgPreviews = document.getElementById('imagePreviews');
                    imgPreviews.innerHTML = ''; // Clear existing previews
                    if (data.product_images && data.product_images.trim() !== '') {
                        $('#productImages').removeAttr('required');
                        const existingImages = data.product_images.split(','); // Split the image string into an array
                        existingImages.forEach(src => {
                            const img = document.createElement('img');
                            img.src = '<?= base_url('uploads/products/') ?>' + src; // Make sure to prepend the correct path
                            img.style.width = '100px';
                            img.style.height = 'auto';
                            img.style.margin = '5px';
                            imgPreviews.appendChild(img);
                        });
                    } else {
                        $('#productImages').attr('required', true);
                    }

                    // Show the modal
                    $('#newProductModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch product data:', error);
                }
            });
        });

    });

    function previewImages(event, previewContainerId) {
        const files = event.target.files;
        const previewContainer = document.getElementById(previewContainerId);

        // Check if the number of selected files exceeds the limit
        if (files.length + previewContainer.children.length > 6) {
            alert('You can upload a maximum of 6 images.');
            // Reset the input value if the limit is exceeded
            event.target.value = '';
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = 'auto';
                img.style.margin = '5px';
                previewContainer.appendChild(img);
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    }

    // Handle delete functionality
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
                    url: "<?= site_url('product-master/delete/') ?>" + dataID,
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
</script>
<?= $this->endSection() ?>