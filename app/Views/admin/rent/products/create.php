<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .dataTables_filter {
        margin-top: 15px !important;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Create Product</h4>
                    <div class="page-title-right">
                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form Section -->
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Product Details</h4>

                        <form class="custom-validation" id="userForm" method='post' action="<?= site_url('rent-products/save') ?>" enctype='multipart/form-data'>
                            <?= \Config\Services::validation()->listErrors(); ?>
                            <?= csrf_field() ?>
                            <?= view('flash_messages'); ?>
                            <input type="hidden" id="rentProductId" name="product_id" value="<?= isset($productDetails["product_id"]) ? esc($productDetails["product_id"]) : ''; ?>">

                            <!-- Product Selection Toggle -->
                            <div class="mb-3">
                                <label class="fw-bold">Select Product Type:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="product_source" id="masterProductRadio" value="master" onchange="loadProducts(this.value)"
                                        <?= (isset($productDetails['product_source']) && $productDetails['product_source'] == 'master') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="masterProductRadio">Master Products</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="product_source" id="purchaseProductRadio" value="purchase_products" onchange="loadProducts(this.value)"
                                        <?= (isset($productDetails['product_source']) && $productDetails['product_source'] == 'purchase_products') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="purchaseProductRadio">Purchase Products</label>
                                </div>
                            </div>

                            <!-- Product Selection -->
                            <div class="mb-3">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px;">
                                    <table id="productTable" class="table table-striped table-bordered">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th data-sortable="true">Select</th>
                                                <th data-sortable="true">Product Name</th>
                                                <th data-sortable="true">Serial Number</th>
                                                <th data-sortable="true">Size</th>
                                                <th data-sortable="true">Colour</th>
                                                <th data-sortable="true">Glass Type</th>
                                                <th data-sortable="true">Touch Type</th>
                                                <th data-sortable="true">Resolution Type</th>
                                                <th data-sortable="false">Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($productDetails['product_source'])) {
                                                if ($productDetails['product_source'] == 'master') {
                                                    foreach ($masterProducts as $product) { ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="form-check-input" name="product_master_id" value="<?= esc($product->product_master_id); ?>"
                                                                    <?php if (isset($productDetails) && $product->product_master_id == $productDetails['product_master_id']) echo 'checked'; ?>
                                                                    style="width: 18px; height: 18px;">
                                                            </td>
                                                            <td><?= esc($product->product_name); ?></td>
                                                            <td><?= esc('N/A'); ?></td>
                                                            <td><?= esc($product->size_in_inches); ?> inches</td>
                                                            <td><?= esc($product->color_name); ?></td>
                                                            <td><?= esc($product->glass_name); ?></td>
                                                            <td><?= esc($product->touch_name); ?></td>
                                                            <td><?= esc($product->resolution_type); ?></td>
                                                            <td>
                                                                <img src="<?= base_url('uploads/products/' . explode(',', $product->product_images)[0]); ?>" class="img-thumbnail" style="max-height: 50px; object-fit: cover;">
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } else {
                                                    foreach ($purchaseProducts as $product) { ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="form-check-input" name="product_master_id" value="<?= esc($product['product_master_id']); ?>"
                                                                    <?php if (isset($productDetails) && $product['product_master_id'] == $productDetails['product_master_id']) echo 'checked'; ?>
                                                                    style="width: 18px; height: 18px;">
                                                            </td>
                                                            <td><?= esc($product['product_name']); ?></td>
                                                            <td><?= esc($product['serial_number'] ?? 'N/A'); ?></td>
                                                            <td><?= esc($product['size_in_inches']); ?> inches</td>
                                                            <td><?= esc($product['color_name']); ?></td>
                                                            <td><?= esc($product['glass_name']); ?></td>
                                                            <td><?= esc($product['touch_name']); ?></td>
                                                            <td><?= esc($product['resolution_type']); ?></td>
                                                            <td>
                                                                <img src="<?= base_url('uploads/products/' . explode(',', $product['product_images'])[0]); ?>" class="img-thumbnail" style="max-height: 50px; object-fit: cover;">
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Price & Quantity -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pricePerDay" class="form-label fw-bold">Price per Day</label>
                                        <input type="number" class="form-control" id="pricePerDay" name="price_per_day" value="<?= isset($productDetails["price_per_day"]) ? esc($productDetails["price_per_day"]) : ''; ?>" required placeholder="Enter price per day" min="0" step="0.01">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productQuantity" class="form-label fw-bold">Total Quantity</label>
                                        <input type="number" class="form-control" id="productQuantity" name="quantity" value="<?= isset($productDetails["quantity"]) ? esc($productDetails["quantity"]) : ''; ?>" required placeholder="Enter quantity" min="1">
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end">
                                <button type="button" class="btn btn-outline-danger me-2" onclick="window.history.back();">Cancel</button>
                                <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables & Checkbox Logic -->
<script>
    $(document).ready(function() {
        $('#productTable').DataTable({
            paging: false, // Disable pagination
            info: false, // Hide table info
            searching: true, // Enable search box
            ordering: true // Enable column sorting
        });

        // Checkbox logic to allow only one checkbox to be checked
        $('#productTable').on('change', 'input[name="product_master_id"]', function() {
            // Uncheck all checkboxes except the one that was clicked
            $('input[name="product_master_id"]').not(this).prop('checked', false);
        });

    });

    $(document).on('change', '.productCheckbox', function() {
        $('.productCheckbox').not(this).prop('checked', false);

        // Check if the selected product is a purchase product
        var isPurchaseProduct = $(this).attr('data-source') === 'purchase_products';

        if (isPurchaseProduct) {
            $('#productQuantity').val(1).prop('readonly', true);
        } else {
            $('#productQuantity').val('').prop('readonly', false);
        }
    });

    function loadProducts(source) {
        $.ajax({
            url: '<?= site_url('get-products-details') ?>',
            method: 'GET',
            data: {
                product_source: source
            },
            success: function(response) {
                var productTable = $('#productTable').DataTable();
                productTable.clear().draw();

                if (response.status === 'success') {
                    var isPurchaseProduct = response.data.product_source === 'purchase_products';
                    var products = response.data.products || response.data.purchaseProducts || [];

                    products.forEach(function(product) {
                        var productId = product.product_master_id;
                        var row = `
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input productCheckbox" name="product_master_id" value="${productId}" data-source="${isPurchaseProduct ? 'purchase_products' : 'master'}" style="width: 20px; height: 20px;">
                                    </div>
                                </td>
                                <td>${product.product_name}</td>
                                ${isPurchaseProduct ? `<td>${product.serial_number ?? 'N/A'}</td>` : '<td>N/A</td>'}
                                <td>${product.size_in_inches} inches</td>
                                <td>${product.color_name}</td>
                                <td>${product.glass_name}</td>
                                <td>${product.touch_name}</td>
                                <td>${product.resolution_type}</td>
                                <td>
                                    <img src="<?= base_url() ?>${product.product_images ? '/uploads/products/' + product.product_images.split(',')[0] : ''}" alt="${product.product_name}" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                </td>
                            </tr>
                        `;
                        productTable.row.add($(row)).draw();
                    });

                } else {
                    alert('Failed to load products');
                }
            },
            error: function() {
                alert('An error occurred while loading products');
            }
        });
    }
</script>

<?= $this->endSection() ?>