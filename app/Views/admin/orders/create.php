<!-- Start Modal -->
<div class="modal fade" id="newOrderModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newOrderModalLabel">Add Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="orderForm" autocomplete="off">
                    <input type="hidden" id="orderId" name="order_id">

                    <!-- Select Product -->
                    <div class="mb-3">
                        <label for="productId" class="form-label">Select Product</label>
                        <div id="productTableContainer" style="max-height: 340px; overflow-y: auto;">
                            <table id="datatable2" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Product Name</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <!-- <th>Quantity</th> -->
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="product_master_id" value="<?= esc($product->product_master_id); ?>">
                                                    <label class="form-check-label"><?= esc($product->product_name); ?></label>
                                                </div>
                                            </td>
                                            <td><?= esc($product->product_name); ?></td>
                                            <td><?= esc($product->color_name); ?></td>
                                            <td><?= esc($product->size_in_inches); ?> inches</td>
                                            <!-- <td></?= esc($product->quantity); ?></td> -->
                                            <td>
                                                <?php
                                                $productImages = explode(',', $product->product_images); // Split the image filenames
                                                $imageUrl = base_url('uploads/products/' . $productImages[0]); // Use the first image for display
                                                ?>
                                                <img src="<?= $imageUrl ?>" alt="<?= $product->product_name ?>" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Product Type Selection -->
                    <div class="mb-3">
                        <label for="productType" class="form-label">Select Product Type</label>
                        <select id="productType" class="form-control" name="rent_or_sale" required onchange="productTypeVisibility()">
                            <option value="">Select</option>
                            <option value="Rent">Rent</option>
                            <option value="Sale">Sale</option>
                        </select>
                    </div>

                    <!-- Rent Fields -->
                    <div id="rentFields" style="display: none;">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" required>
                        </div>
                    </div>

                    <!-- Customer Name -->
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="customerName" name="customer_name" required placeholder="Enter customer name">
                    </div>

                    <!-- Customer Mobile -->
                    <div class="mb-3">
                        <label for="customerMobile" class="form-label">Customer Mobile<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="customerMobile" name="customer_mobile" required placeholder="Enter customer mobile" maxlength="20">
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required placeholder="Enter quantity" min="1">
                    </div>

                    <!-- Product Price -->
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Product Price</label>
                        <input type="number" class="form-control" id="productPrice" name="product_price" placeholder="Enter product price" min="0" step="0.01">
                    </div>

                    <!-- Discount -->
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount</label>
                        <input type="number" class="form-control" id="discount" name="discount" placeholder="Enter discount" min="0" step="0.01">
                    </div>

                    <!-- Final Product Price -->
                    <div class="mb-3">
                        <label for="finalProductPrice" class="form-label">Final Product Price</label>
                        <input type="number" class="form-control" id="finalProductPrice" name="final_product_price" placeholder="Final price after discount" readonly>
                    </div>

                    <!-- Payment Status -->
                    <div class="mb-3">
                        <label for="paymentStatus" class="form-label">Payment Status<span class="mandatory-field">*</span></label>
                        <select id="paymentStatus" class="form-control" name="payment_status" required>
                            <option value="">Select Payment Status</option>
                            <option value="Success">Success</option>
                            <option value="Pending">Pending</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Payment Details -->
                    <div class="mb-3">
                        <label for="paymentDetails" class="form-label">Payment Details</label>
                        <textarea class="form-control" id="paymentDetails" name="payment_details" placeholder="Enter payment details"></textarea>
                    </div>

                    <!-- Uploaded Receipt -->
                    <div class="mb-3">
                        <label for="uploadedReceipt" class="form-label">Upload Receipt</label>
                        <input type="file" class="form-control" id="uploadedReceipt" name="uploaded_receipt">
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
    // Apply scroll functionality after 3 rows
    document.addEventListener("DOMContentLoaded", function() {
        const tableContainer = document.getElementById("productTableContainer");
        const rows = tableContainer.querySelectorAll("tbody tr");

        // Check if there are more than 3 rows
        if (rows.length > 3) {
            // Apply scroll to the container
            tableContainer.style.maxHeight = '340px';
            tableContainer.style.overflowY = 'auto';
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const checkboxes = document.querySelectorAll('input[name="product_master_id"]');

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // If the checkbox is checked, uncheck all other checkboxes
                checkboxes.forEach(function(otherCheckbox) {
                    if (otherCheckbox !== checkbox) {
                        otherCheckbox.checked = false;
                    }
                });
            });
        });
    });
</script>