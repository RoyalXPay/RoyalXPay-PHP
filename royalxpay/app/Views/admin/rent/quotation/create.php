<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Rent Quotation</a></li>
                        <li class="breadcrumb-item active">Create Quotation</li>
                    </ol>
                    <div class="page-title-right">
                        <a href="<?= site_url('rent-quotations'); ?>" class="btn btn-secondary waves-effect waves-light">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <form class="custom-validation" method='post' action="<?= site_url('rent-quotations/save') ?>" enctype='multipart/form-data'>
                                <?= \Config\Services::validation()->listErrors(); ?>
                                <?= csrf_field() ?>
                                <?= view('flash_messages'); ?>
                                <h4 class="card-title"><i class="fas fa-code-branch"></i> Create Rent Quotation</h4>
                                <p class="card-subtitle mb-2 text-muted">Fill in the details below to create or edit a new Rent Quotation.</p>
                                <input type="hidden" id="quotationId" name="quotation_id" value="<?= $records['quotation_id'] ?? ""; ?>">
                                <input type="hidden" id="quotationCode" name="quotation_code" value="<?= $records['quotation_code'] ?? ""; ?>">

                                <!-- Customer Information -->
                                <div class="mb-3 row">
                                    <div class="col-10">
                                        <label for="customerName" class="form-label">Select Customer or Add New Customer<span class="mandatory-field">*</span></label>
                                        <select class="form-select" id="customerName" name="customer_id" required>
                                            <option value="">Select a customer</option>
                                            <?php $userId = $records['user_id'] ?? ""; ?>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= htmlspecialchars($customer->user_id); ?>" <?php echo ($customer->user_id == $userId) ? "selected" : ""; ?>>
                                                    <?= htmlspecialchars($customer->name) . ' ' . '(' . htmlspecialchars($customer->phone) . ')'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-2 d-flex align-items-end">
                                        <a href="<?= site_url('customers'); ?>" class="btn btn-primary w-100">Add</a>
                                    </div>
                                </div>

                                <!-- Rental Dates -->
                                <div class="mb-3">
                                    <label for="startDate" class="form-label">Start Date<span class="mandatory-field">*</span></label>
                                    <input type="date" class="form-control" id="startDate" name="start_date" required value="<?= $records['start_date'] ?? ''; ?>" placeholder="Select start date">
                                </div>
                                <div class="mb-3">
                                    <label for="endDate" class="form-label">End Date<span class="mandatory-field">*</span></label>
                                    <input type="date" class="form-control" id="endDate" name="end_date" required value="<?= $records['end_date'] ?? ''; ?>" placeholder="Select end date">
                                </div>

                                <!-- Button to fetch products -->
                                <div class="mb-3">
                                    <button type="button" id="fetchProductsButton" class="btn btn-primary" onclick="fetchProducts()">Fetch Products</button>
                                </div>

                                <!-- Select Product Section -->
                                <div class="mb-3">
                                    <table id="productTable" class="table table-bordered table-striped dt-responsive w-100">
                                        <thead>
                                            <tr>
                                                <th>Select</th>
                                                <th data-sortable="true" class="text-center">Product Name</th>
                                                <th data-sortable="true" class="text-center">Serial Number</th>
                                                <th data-sortable="true" class="text-center">Size</th>
                                                <th data-sortable="true" class="text-center">Colour</th>
                                                <th data-sortable="true" class="text-center">Glass Type</th>
                                                <th data-sortable="true" class="text-center">Touch/ Non Touch</th>
                                                <th data-sortable="true" class="text-center">Quantity</th>
                                                <th data-sortable="true" class="text-center">Price Per day</th>
                                                <th data-sortable="false" class="text-center">Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($records) && !empty($records)) { ?>
                                                <?php foreach ($rentProducts as $product):
                                                    $isPurchase = $product->master_product_type === 'purchase';
                                                    // Check if there's a matching entry in the $quotationProductMap using the product_id
                                                    $quotationProduct = isset($quotationProductMap[$product->product_id]) ? $quotationProductMap[$product->product_id] : null;
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input productCheckbox" name="productIds[]" value="<?php echo $product->product_id ?>" style="width: 20px; height: 20px;" <?php echo ($quotationProduct) ? 'checked' : ''; ?>>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $product->product_name ?></td>
                                                        <td><?php echo ($isPurchase ? ($product->serial_number ?? 'N/A') : 'N/A'); ?></td>
                                                        <td><?php echo $product->size_in_inches ?> inches</td>
                                                        <td><?php echo $product->color_name ?></td>
                                                        <td><?php echo $product->glass_name ?></td>
                                                        <td><?php echo $product->touch_name ?></td>
                                                        <td>
                                                            <input type="number" class="form-control quantity-input" name="quantity[]" value="<?php echo ($quotationProduct) ? $quotationProduct['quantity'] : $product->available_quantity; ?>" max="<?php echo $product->available_quantity; ?>" style="width: 100px;">
                                                        </td>
                                                        <td><?php echo $product->price_per_day; ?></td>
                                                        <td>
                                                            <?php
                                                            $productImages = explode(',', $product->product_images);
                                                            $imageUrl = base_url('uploads/products/' . $productImages[0]);
                                                            ?>
                                                            <img src="<?= $imageUrl ?>" alt="<?= $product->product_name ?>" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>

                                            <?php } ?>
                                            <!-- Product rows will be dynamically added here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Dynamic Calculations -->
                                <div class="mb-3">
                                    <label for="totalPrice" class="form-label">Total Amount Before Discount<span class="mandatory-field">*</span></label>
                                    <input type="text" class="form-control" id="totalPrice" name="total_price" readonly value="<?= $records['total_price'] ?? ''; ?>" placeholder="Total price will appear here">
                                </div>

                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount %</label>
                                    <input type="number" class="form-control" id="discount" name="discount" value="<?= $records['discount'] ?? ''; ?>" placeholder="Enter discount percentage" min="0" max="100" step="0.01">
                                </div>

                                <div class="mb-3">
                                    <label for="vat" class="form-label">VAT (5%)<span class="mandatory-field">*</span></label>
                                    <input type="text" class="form-control" id="vat" name="vat" readonly value="<?= $records['vat'] ?? ''; ?>" placeholder="VAT will appear here">
                                </div>


                                <div class="mb-3">
                                    <label for="finalPayablePrice" class="form-label">Final Payable Amount<span class="mandatory-field">*</span></label>
                                    <input type="text" class="form-control" id="finalPayablePrice" name="final_payable_price" value="<?= $records['final_payable_price'] ?? ''; ?>" readonly placeholder="Final price will appear here">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Citylight Information</label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_logo" id="citylightLogo" <?= (isset($quotationDetails->citylight_logo) && $quotationDetails->citylight_logo == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightLogo">Citylight Logo</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_address" id="citylightAddress" <?= (isset($quotationDetails->citylight_address) && $quotationDetails->citylight_address == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightAddress">Citylight Address</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_contact_details" id="citylightContact" <?= (isset($quotationDetails->citylight_contact_details) && $quotationDetails->citylight_contact_details == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightContact">Citylight Contact Details</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_email" id="citylightEmail" <?= (isset($quotationDetails->citylight_email) && $quotationDetails->citylight_email == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightEmail">Citylight Email</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_website" id="citylightWebsite" <?= (isset($quotationDetails->citylight_website) && $quotationDetails->citylight_website == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightWebsite">Citylight Website</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="bank_details" id="bankDetails" <?= (isset($quotationDetails->bank_details) && $quotationDetails->bank_details == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="bankDetails">Bank Details</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="return_policies" id="returnPolicies" <?= (isset($quotationDetails->return_policies) && $quotationDetails->return_policies == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="returnPolicies">Return Policies</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="signature" id="signature" <?= (isset($quotationDetails->signature) && $quotationDetails->signature == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="signature">Signature</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="stamp" id="stamp" <?= (isset($quotationDetails->stamp) && $quotationDetails->stamp == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="stamp">Stamp</label>
                                    </div>
                                </div>

                                <!-- Delivery Time -->
                                <div class="mb-3">
                                    <label for="deliveryTimes" class="form-label">Delivery Time for Rent</label>
                                    <select id="deliveryTimes" name="delivery_time" class="form-select" placeholder="Select delivery time for rent">
                                        <option value="">Select Delivery Time for Rent</option>
                                        <?php
                                        // Ensure delivery_time exists in the records object, if not default to an empty string
                                        $selectedDeliveryTime = isset($records['delivery_time']) ? $records['delivery_time'] : "";
                                        ?>
                                        <?php if (isset($deliveryTimes) && is_array($deliveryTimes)): ?>
                                            <?php foreach ($deliveryTimes as $deliveryTime): ?>
                                                <option value="<?= esc($deliveryTime->id); ?>" <?= ($deliveryTime->id == $selectedDeliveryTime) ? "selected" : ""; ?>><?= esc($deliveryTime->title); ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No delivery times available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="mb-3">
                                    <label for="termsConditions" class="form-label">Select Terms and Conditions</label>
                                    <select id="termsConditions" name="terms_conditions" class="form-select" placeholder="Select terms and conditions">
                                        <option value="">Select Terms and Conditions</option>
                                        <?php
                                        // Ensure terms_conditions exists in the records object, if not default to an empty string
                                        $selectedTermsConditions = isset($records['terms_conditions']) ? $records['terms_conditions'] : "";
                                        ?>
                                        <?php if (isset($terms) && is_array($terms)): ?>
                                            <?php foreach ($terms as $term): ?>
                                                <option value="<?= esc($term->id); ?>" <?= ($term->id == $selectedTermsConditions) ? "selected" : ""; ?>><?= esc($term->title); ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No terms and conditions available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Notes -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any additional notes here"><?= isset($records['notes']) ? esc($records['notes']) : ''; ?></textarea>
                                </div>

                                <!-- Modal Footer -->
                                <div class="text-end">
                                    <a href="<?= site_url('rent-quotations'); ?>" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                                </div>

                            </form>
                        </div>

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div> <!-- container-fluid -->
</div><!-- End Page-content -->
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#productTable').DataTable({
            "info": true,
            "paging": true,
            "ordering": true,
            "searching": true,
            "pageLength": 50,
            "lengthMenu": [5, 10, 25, 50, 100]
        });

        var today = new Date().toISOString().split('T')[0];

        // Set only if the input fields are empty
        if (!$('#startDate').val()) {
            $('#startDate').val(today);
        }
        if (!$('#endDate').val()) {
            $('#endDate').val(today);
        }

        // Event listener for when a checkbox is clicked, quantity is changed, or discount is entered
        $(document).on('change', '.productCheckbox, .quantity-input, #discount, #startDate, #endDate', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let subtotal = 0;
            let discount = parseFloat($('#discount').val()) || 0;

            // Get the number of days (inclusive of start and end dates)
            let noOfDays = getDaysBetweenDates($('#startDate').val(), $('#endDate').val());

            // Calculate subtotal by summing up selected products
            $('.productCheckbox:checked').each(function() {
                let row = $(this).closest('tr');
                let pricePerDay = parseFloat(row.find('td:nth-child(9)').text()) || 0;
                let quantity = parseInt(row.find('.quantity-input').val()) || 0;

                if (quantity > 0) {
                    subtotal += pricePerDay * quantity * noOfDays;
                }
            });

            // Calculate discount amount
            let discountAmount = (subtotal * discount) / 100;

            // Subtotal after discount
            let discountedTotal = subtotal - discountAmount;

            // Calculate VAT (5%)
            let vat = discountedTotal * 0.05;

            // Calculate final payable amount
            let finalPayablePrice = discountedTotal + vat;

            // Update the fields with the calculated values
            $('#totalPrice').val(subtotal.toFixed(3));
            $('#vat').val(vat.toFixed(3));
            $('#finalPayablePrice').val(finalPayablePrice.toFixed(3));
        }

        function getDaysBetweenDates(startDate, endDate) {
            if (!startDate || !endDate) {
                return 0;
            }
            const start = new Date(startDate);
            const end = new Date(endDate);

            // Calculate the difference in time and convert to days
            const timeDiff = end.getTime() - start.getTime();
            const days = timeDiff / (1000 * 3600 * 24);

            // Include both start and end date in the count
            return days >= 0 ? days + 1 : 0;
        }

        $('#submitButton').on('click', function() {

            var selectedProductIds = [];
            var selectedQuantities = [];
            var selectedPricePerDays = [];

            // Loop through each product checkbox to get selected products
            $('.productCheckbox').each(function() {
                if ($(this).prop('checked')) {
                    var quantityInput = $(this).closest('tr').find('.quantity-input');
                    var quantity = parseInt(quantityInput.val()) || 0;
                    var pricePerDay = parseFloat($(this).closest('tr').find('td').eq(8).text().trim()) || 0;

                    selectedProductIds.push($(this).val());
                    selectedQuantities.push(quantity);
                    selectedPricePerDays.push(pricePerDay);
                }
            });

            // Validate if no product is selected
            if (selectedProductIds.length === 0) {
                showError("Please select at least one product.");
                return;
            }

            // Create hidden inputs dynamically for the selected data
            var productIdsInput = $('<input>', {
                type: 'hidden',
                name: 'productIds',
                value: selectedProductIds.join(',')
            });
            var quantitiesInput = $('<input>', {
                type: 'hidden',
                name: 'quantity',
                value: selectedQuantities.join(',')
            });
            var pricePerDaysInput = $('<input>', {
                type: 'hidden',
                name: 'price_per_day',
                value: selectedPricePerDays.join(',')
            });

            // Append the hidden inputs to the form
            $(this).append(productIdsInput, quantitiesInput, pricePerDaysInput);

        });

    });

    function fetchProducts() {
        const table = $('#productTable').DataTable();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const productTableBody = document.querySelector('#productTable tbody');

        // Clear the table before loading new products
        productTableBody.innerHTML = '';

        // Check if both dates are selected
        if (!startDate || !endDate) {
            productTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Please select both start date and end date.</td></tr>';
            return;
        }

        // Show loading message while fetching products
        productTableBody.innerHTML = '<tr><td colspan="9" class="text-center">Loading products...</td></tr>';

        $.ajax({
            type: 'GET',
            url: '<?= site_url('rent-quotations/fetch-rent-products') ?>',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
                productTableBody.innerHTML = '';

                if (response && response.products && response.products.length > 0) {
                    response.products.forEach(function(product) {
                        const isAvailable = product.available_quantity > 0;
                        const isPurchase = product.master_product_type === 'purchase';

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input productCheckbox" name="productIds[]" value="${product.product_id}" ${isAvailable ? '' : 'disabled'} style="width: 20px; height: 20px;">
                                </div>
                            </td>
                            <td>${product.product_name}</td>
                            <td>${isPurchase ? (product.serial_number ?? 'N/A') : 'N/A'}</td>
                            <td>${product.size_in_inches} inches</td>
                            <td>${product.color_name}</td>
                            <td>${product.glass_name}</td>
                            <td>${product.touch_name}</td>
                            <td>
                                <input type="number" class="form-control quantity-input" name="quantity[${product.product_id}]" value="${product.available_quantity}" min="${isAvailable ? 1 : 0}" max="${product.available_quantity}" style="width: 100px;">
                            </td>
                            <td>${product.price_per_day}</td>
                            <td>
                                <img src="${product.product_images ? '<?= base_url() ?>' + '/uploads/products/' + product.product_images.split(',')[0] : ''}" alt="${product.product_name}" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                            </td>
                        `;
                        productTableBody.appendChild(row);
                    });

                    table.clear();
                    $('#productTable tbody tr').each(function() {
                        table.row.add(this);
                    });
                    table.draw();
                } else {
                    productTableBody.innerHTML = '<tr><td colspan="10" class="text-center">No products available for the selected dates.</td></tr>';
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching products:", xhr.responseText || error);
                productTableBody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error loading products. Please try again later.</td></tr>';
            }
        });
    }
</script>

<?= $this->endSection() ?>