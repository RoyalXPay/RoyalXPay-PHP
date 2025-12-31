<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Purchases</a></li>
                        <li class="breadcrumb-item active"><?= isset($record) ? 'Edit' : 'Create'; ?> Purchase Entry</li>
                    </ol>
                    <div class="page-title-right">
                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
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
                            <form class="custom-validation" method="post" action="<?= site_url('purchases/save') ?>" enctype="multipart/form-data">
                                <?= \Config\Services::validation()->listErrors(); ?>
                                <?= csrf_field() ?>
                                <?= view('flash_messages'); ?>

                                <h4 class="card-title"><i class="fas fa-code-branch"></i> <?= isset($record) ? 'Edit' : 'Create'; ?> Purchase</h4>
                                <p class="card-subtitle mb-4 text-muted">Fill in the details below to create or edit a new Purchase.</p>

                                <!-- Hidden input for purchase ID -->
                                <input type="hidden" id="purchaseId" name="purchase_id" value="<?= $record['purchase_id'] ?? ''; ?>">

                                <!-- Company Selection -->
                                <div class="mb-3">
                                    <label for="company" class="form-label">Company <span class="mandatory-field">*</span></label>
                                    <select class="form-select" id="company" name="company_id" required>
                                        <option value="">Select Company</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?= esc($company->company_id); ?>" <?= (isset($record) && $record['company_id'] == $company->company_id) ? 'selected' : ''; ?>>
                                                <?= esc($company->company_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Product Master Table -->
                                <div class="mb-3">
                                    <label class="form-label">Select Product Purchase</label>
                                    <div style="max-height: 500px; overflow-y: auto;">
                                        <table id="productTable" class="table table-bordered table-striped dt-responsive w-100">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th class="text-center">Product Name</th>
                                                    <th class="text-center">Size</th>
                                                    <th class="text-center">Colour</th>
                                                    <th class="text-center">Glass Type</th>
                                                    <th class="text-center">Touch/ Non Touch</th>
                                                    <th class="text-center">Quantity</th>
                                                    <th class="text-center">Price Per Unit</th>
                                                    <th class="text-center">CBM</th>
                                                    <th class="text-center">Shipment Price</th>
                                                    <th class="text-center" data-orderable="false">Image</th>
                                                    <th class="text-center">Serial Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($products as $product): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input productCheckbox" name="productIds[]" value="<?= esc($product->product_master_id); ?>" style="width: 20px; height: 20px;" <?= isset($record) && in_array($product->product_master_id, array_column($record['purchaseItems'], 'product_master_id')) ? 'checked' : ''; ?>>
                                                            </div>
                                                        </td>
                                                        <td><?= esc($product->product_name); ?></td>
                                                        <td><?= esc($product->size_in_inches); ?> inches</td>
                                                        <td><?= esc($product->color_name); ?></td>
                                                        <td><?= esc($product->glass_name); ?></td>
                                                        <td><?= esc($product->touch_name); ?></td>
                                                        <td>
                                                            <input type="number" class="form-control product-quantity" data-product-id="<?= esc($product->product_master_id); ?>" name="quantity[]" placeholder="Quantity"
                                                                value="<?= isset($record) ? $record['purchaseItems'][$product->product_master_id]['quantity'] ?? '' : ''; ?>" min="1" style="width: 100px;">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control price-per-unit-input" name="price_per_unit[]" placeholder="Price Per Unit"
                                                                value="<?= isset($record) ? $record['purchaseItems'][$product->product_master_id]['price_per_unit'] ?? esc($product->product_price) : esc($product->product_price); ?>" min="1" step="0.001" style="width: 100px;">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control cbm-input" name="cbm_amount[]" placeholder="CBM"
                                                                value="<?= isset($record) ? $record['purchaseItems'][$product->product_master_id]['cbm_amount'] ?? '' : ''; ?>" style="width: 100px;">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control shipment-price-input" name="shipment_price[]" placeholder="Shipment Price"
                                                                value="<?= isset($record) ? $record['purchaseItems'][$product->product_master_id]['shipment_price'] ?? '' : ''; ?>" style="width: 100px;">
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $productImages = explode(',', $product->product_images);
                                                            $imageUrl = base_url('uploads/products/' . $productImages[0]);
                                                            ?>
                                                            <img src="<?= $imageUrl ?>" alt="<?= $product->product_name ?>" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                                        </td>

                                                        <td>
                                                            <button type="button" class="btn btn-primary serial-btn"
                                                                data-product-id="<?= esc($product->product_master_id); ?>">
                                                                Enter Serial Number
                                                            </button>
                                                        </td>

                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                        <!-- Hidden Serial Numbers Field (will be populated dynamically) -->
                                        <div id="serialNumbersContainer">
                                            <?php if (isset($record['purchaseItems'])): ?>
                                                <?php foreach ($record['purchaseItems'] as $item): ?>
                                                    <?php if (!empty($item['serial_numbers'])): ?>
                                                        <div class="serial-group" data-product-id="<?= $item['product_master_id']; ?>">
                                                            <?php foreach ($item['serial_numbers'] as $serial): ?>
                                                                <input type="hidden" name="serial_numbers[<?= $item['product_master_id']; ?>][]" value="<?= esc($serial); ?>">
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>

                                <!-- Payment Details Section -->
                                <fieldset class="mb-4 border p-4 shadow-sm rounded">
                                    <legend class="fw-bold bg-white">Payment Details</legend>
                                    <div class="mb-3">
                                        <label for="totalProductAmount" class="form-label">Total product Amount</label>
                                        <input type="text" class="form-control" id="totalProductAmount" name="product_amount" readonly placeholder="Total product amount will appear here" value="<?= isset($record) ? $record['product_amount'] : ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="exportWoodenCase" class="form-label">Export Wooden Case</label>
                                        <input type="text" class="form-control" id="exportWoodenCase" name="export_wooden_case" value="<?= isset($record) ? $record['export_wooden_case'] : ''; ?>" placeholder="Details about Export Wooden Case">
                                    </div>
                                    <div class="mb-3">
                                        <label for="sendToFoshanWarehouse" class="form-label">Send to Customer's Foshan Warehouse</label>
                                        <input type="text" class="form-control" id="sendToFoshanWarehouse" name="send_to_foshan_warehouse" value="<?= isset($record) ? $record['send_to_warehouse'] : ''; ?>" placeholder="Details about sending to Foshan warehouse">
                                    </div>

                                    <div class="mb-3">
                                        <label for="totalAmount" class="form-label">Total Amount</label>
                                        <input type="text" class="form-control" id="totalAmount" name="total_amount" readonly placeholder="Total price will appear here" value="<?= isset($record) ? $record['total_amount'] : ''; ?>">
                                    </div>

                                    <!-- Was China Paid -->
                                    <div class="mb-3">
                                        <label for="wasChinaPaid" class="form-label">Was China Paid</label>
                                        <select class="form-select" id="wasChinaPaid" name="was_china_paid">
                                            <option value="">Select Option</option>
                                            <option value="yes" <?= (isset($record) && $record['was_china_paid'] == 'yes') ? 'selected' : ''; ?>>Yes</option>
                                            <option value="no" <?= (isset($record) && $record['was_china_paid'] == 'no') ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </div>

                                    <!-- Expected Payment Date -->
                                    <div class="mb-3">
                                        <label for="expectedPaymentDate" class="form-label">Expected Payment Date</label>
                                        <input type="date" class="form-control" id="expectedPaymentDate" name="payment_date" value="<?= isset($record) ? $record['payment_date'] : ''; ?>">
                                    </div>

                                    <!-- Payment Status -->
                                    <div class="mb-3">
                                        <label for="paymentStatus" class="form-label">Payment Status</label>
                                        <select name="payment_status" class="form-select">
                                            <option value="">Select Payment Status</option>
                                            <option value="paid" <?= (isset($record) && $record['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                            <option value="pending" <?= (isset($record) && $record['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="partially_paid" <?= (isset($record) && $record['payment_status'] == 'partially_paid') ? 'selected' : ''; ?>>Partially Paid</option>
                                            <option value="not_paid" <?= (isset($record) && $record['payment_status'] == 'not_paid') ? 'selected' : ''; ?>>Not Paid</option>
                                        </select>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="mb-3">
                                        <label for="paymentMethod" class="form-label">Payment Method</label>
                                        <select class="form-select" id="paymentMethod" name="payment_method">
                                            <option value="">Select Payment Method</option>
                                            <option value="cash" <?= (isset($record) && $record['payment_method'] == 'cash') ? 'selected' : ''; ?>>Cash</option>
                                            <option value="online" <?= (isset($record) && $record['payment_method'] == 'online') ? 'selected' : ''; ?>>Online</option>
                                            <option value="bank_transfer" <?= (isset($record) && $record['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="other" <?= (isset($record) && $record['payment_method'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>

                                    <!-- Receipt Upload -->
                                    <div class="mb-3">
                                        <label for="uploadedReceipt" class="form-label">Upload Receipt</label>
                                        <input class="form-control" type="file" id="uploadedReceipt" name="uploaded_receipt" accept="image/*, application/pdf">
                                        <?php if (isset($record['uploaded_receipt'])): ?>
                                            <a href="<?= base_url('uploads/receipts/' . $record['uploaded_receipt']) ?>" target="_blank">
                                                <i class="fas fa-download"></i> Download Receipt
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label for="purchaseStatus" class="form-label">Status</label>
                                        <select class="form-select" name="purchase_status" id="purchaseStatus">
                                            <option value="">Select Status</option>
                                            <option value="pending" <?= (isset($record) && $record['purchase_status'] == "pending") ? "selected" : ""; ?>>Pending</option>
                                            <option value="completed" <?= (isset($record) && $record['purchase_status'] == "completed") ? "selected" : ""; ?>>Completed</option>
                                            <option value="stopped" <?= (isset($record) && $record['purchase_status'] == "stopped") ? "selected" : ""; ?>>Stopped</option>
                                            <option value="cancelled" <?= (isset($record) && $record['purchase_status'] == "cancelled") ? "selected" : ""; ?>>Cancelled</option>
                                        </select>
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="4"><?= isset($record) ? $record['notes'] : ''; ?></textarea>
                                    </div>
                                </fieldset>

                                <!-- Agent Details Section -->
                                <fieldset class="mb-4 border p-4 shadow-sm rounded">
                                    <legend class="fw-bold bg-white">Agent Details</legend>
                                    <div class="mb-3">
                                        <label for="agentPrice" class="form-label">Agent Price</label>
                                        <input type="text" class="form-control" id="agentPrice" name="agent_price" value="<?= isset($record) ? $record['agent_price'] : ''; ?>" placeholder="Enter Agent Price">
                                    </div>
                                    <div class="mb-3">
                                        <label for="agentCBM" class="form-label">Agent CBM</label>
                                        <input type="number" class="form-control" id="agentCBM" name="agent_cbm" value="<?= isset($record) ? $record['agent_cbm'] : ''; ?>" placeholder="Enter Agent CBM" step="any">
                                    </div>
                                    <!-- Agent Payment Status -->
                                    <div class="mb-3">
                                        <label for="agentPaymentStatus" class="form-label">Agent Payment Status</label>
                                        <select name="agent_payment_status" class="form-select">
                                            <option value="">Select Agent Payment Status</option>
                                            <option value="paid" <?= (isset($record) && $record['agent_payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                            <option value="pending" <?= (isset($record) && $record['agent_payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="partially_paid" <?= (isset($record) && $record['agent_payment_status'] == 'partially_paid') ? 'selected' : ''; ?>>Partially Paid</option>
                                            <option value="not_paid" <?= (isset($record) && $record['agent_payment_status'] == 'not_paid') ? 'selected' : ''; ?>>Not Paid</option>
                                        </select>
                                    </div>

                                    <!-- Agent Payment Method -->
                                    <div class="mb-3">
                                        <label for="agentPaymentMethod" class="form-label">Agent Payment Method</label>
                                        <select class="form-select" id="agentPaymentMethod" name="agent_payment_method">
                                            <option value="">Select Agent Payment Method</option>
                                            <option value="cash" <?= (isset($record) && $record['agent_payment_method'] == 'cash') ? 'selected' : ''; ?>>Cash</option>
                                            <option value="online" <?= (isset($record) && $record['agent_payment_method'] == 'online') ? 'selected' : ''; ?>>Online</option>
                                            <option value="bank_transfer" <?= (isset($record) && $record['agent_payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="other" <?= (isset($record) && $record['agent_payment_method'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="agentAttachment" class="form-label">Agent Attachment</label>
                                        <input class="form-control" type="file" id="agentAttachment" name="agent_attachment" accept="image/*, application/pdf">
                                        <?php if (isset($record['agent_attachment'])): ?>
                                            <a href="<?= base_url('uploads/agent/' . $record['agent_attachment']) ?>" target="_blank">
                                                <i class="fas fa-download"></i> Download Attachment
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="agentStatus" class="form-label">Agent Status</label>
                                        <select class="form-select" name="agent_status" id="agentStatus">
                                            <option value="">Select Agent Status</option>
                                            <option value="active" <?= (isset($record) && $record['agent_status'] == "active") ? "selected" : ""; ?>>Active</option>
                                            <option value="inactive" <?= (isset($record) && $record['agent_status'] == "inactive") ? "selected" : ""; ?>>Inactive</option>
                                            <option value="pending" <?= (isset($record) && $record['agent_status'] == "pending") ? "selected" : ""; ?>>Pending</option>
                                            <option value="suspended" <?= (isset($record) && $record['agent_status'] == "suspended") ? "selected" : ""; ?>>Suspended</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="agentNotes" class="form-label">Agent Notes</label>
                                        <textarea class="form-control" id="agentNotes" name="agent_notes" rows="4" placeholder="Enter any notes"><?= isset($record) ? $record['agent_notes'] : ''; ?></textarea>
                                    </div>
                                </fieldset>

                                <!-- Factory / Supplier Product Details Section -->
                                <fieldset class="mb-4 border p-4 shadow-sm rounded">
                                    <legend class="fw-bold bg-white">Factory / Supplier Product Details</legend>
                                    <div class="mb-3">
                                        <label for="finalTotalAmount" class="form-label">Final Total amount</label>
                                        <input type="text" class="form-control" id="finalTotalAmount" name="final_total_amount" readonly placeholder="Final Total Amount will appear here" value="<?= isset($record) ? $record['final_total_amount'] : ''; ?>">
                                    </div>
                                </fieldset>

                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back();">Cancel</button>
                                    <button type="submit" class="btn btn-success"><?= isset($record) ? 'Update' : 'Save'; ?></button>
                                </div>
                            </form>

                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div> <!-- container-fluid -->
</div><!-- End Page-content -->

<!-- Serial Number Modal -->
<div class="modal fade" id="serialNumberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Serial Numbers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalProductId">
                <div id="serialNumberFields"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveSerialNumbers" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable with pagination disabled
        $('#productTable').DataTable({
            "info": true,
            "paging": false,
            "ordering": true,
            "searching": true,
        });

        let serialNumbersData = {}; // Store serial numbers for each product
        let purchaseItemsData = <?= json_encode($record['purchaseItems'] ?? []); ?>;
        const isEditMode = <?= isset($record) ? 'true' : 'false'; ?>;

        // Disable quantity inputs only for existing products in edit mode
        if (isEditMode) {
            $('.product-quantity').each(function() {
                const productId = $(this).data('product-id');
                // Only disable if this product is already in the purchase
                if (purchaseItemsData[productId]) {
                    $(this).prop('readonly', true)
                        .css('background-color', '#f8f9fa')
                        .attr('title', 'Quantity can only be changed by adding/removing serial numbers');
                }

                // Initialize existing serial numbers
                if (purchaseItemsData[productId]?.serial_numbers) {
                    serialNumbersData[productId] = purchaseItemsData[productId].serial_numbers;
                }
            });
        }

        // Click event for "Enter Serial" button
        $(document).on('click', '.serial-btn', function() {
            let productId = $(this).data('product-id');
            let productName = $(this).closest('tr').find('td:eq(1)').text().trim();
            let checkbox = $('input.productCheckbox[value="' + productId + '"]');

            if (!checkbox.prop('checked')) {
                alert(`Please select "${productName}" before entering serial numbers.`);
                return;
            }

            let quantityInput = $('input.product-quantity[data-product-id="' + productId + '"]');
            let currentQuantity = parseInt(quantityInput.val(), 10) || 0;

            // For new products in edit mode, allow direct quantity setting
            const isExistingProduct = isEditMode && purchaseItemsData[productId];

            $('#modalProductId').val(productId);
            $('#serialNumberFields').empty();

            // Retrieve previously saved serial numbers
            let existingSerialNumbers = serialNumbersData[productId] || [];

            // For existing products in edit mode, use existing serial numbers to determine quantity
            if (isExistingProduct && existingSerialNumbers.length > 0) {
                currentQuantity = existingSerialNumbers.length;
                quantityInput.val(currentQuantity);
            }

            // If no quantity set, prompt user
            if (currentQuantity <= 0 && isExistingProduct) {
                alert(`Please enter a valid quantity for "${productName}" first.`);
                return;
            }

            // Create input fields for each serial number
            for (let i = 0; i < (isExistingProduct ? currentQuantity : Math.max(currentQuantity, 1)); i++) {
                let serialValue = existingSerialNumbers[i] ? existingSerialNumbers[i].toUpperCase() : '';
                $('#serialNumberFields').append(`
                    <div class="serial-input-group d-flex align-items-center mb-2">
                        <label class="me-2">Serial ${i + 1}</label>
                        <input type="text" class="form-control serial-input me-2" value="${serialValue}" oninput="this.value = this.value.toUpperCase()" required>
                        <button type="button" class="btn btn-danger remove-serial">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `);
            }

            // Add button to add more serial numbers
            $('#serialNumberFields').append(`
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-sm btn-primary add-serial">
                        <i class="fas fa-plus"></i> Add Serial
                    </button>
                </div>
            `);

            // Update modal title with product name
            $('.modal-title').html(`Enter Serial Numbers for <strong>${productName}</strong>`);
            $('#serialNumberModal').modal('show');
        });

        // Add new serial number field
        $(document).on('click', '.add-serial', function() {
            const productId = $('#modalProductId').val();
            const currentCount = $('.serial-input').length;

            $('#serialNumberFields').find('.text-end').before(`
                <div class="serial-input-group d-flex align-items-center mb-2">
                    <label class="me-2">Serial ${currentCount + 1}</label>
                    <input type="text" class="form-control serial-input me-2" oninput="this.value = this.value.toUpperCase()" required>
                    <button type="button" class="btn btn-danger remove-serial">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `);
        });

        // Remove Serial Number and Update Quantity
        $(document).on('click', '.remove-serial', function() {
            const productId = $('#modalProductId').val();
            const productName = $(`.serial-btn[data-product-id="${productId}"]`).closest('tr').find('td:eq(1)').text().trim();

            if (!confirm(`Are you sure you want to remove this serial number for "${productName}"? This will reduce the quantity by 1.`)) {
                return;
            }

            $(this).closest('.serial-input-group').remove();
            updateQuantityFromSerials();
        });

        function updateQuantityFromSerials() {
            const productId = $('#modalProductId').val();
            const quantityInput = $('input.product-quantity[data-product-id="' + productId + '"]');
            const currentSerialCount = $('.serial-input').length;

            quantityInput.val(currentSerialCount);
        }

        // Save Serial Numbers and Append to Form
        $('#saveSerialNumbers').click(function() {
            const productId = $('#modalProductId').val();
            const productName = $(`.serial-btn[data-product-id="${productId}"]`).closest('tr').find('td:eq(1)').text().trim();
            let serialNumbers = [];
            let hasEmptySerials = false;

            // Validate and collect serial numbers
            $('.serial-input').each(function() {
                const serial = $(this).val().trim().toUpperCase();
                if (!serial) {
                    hasEmptySerials = true;
                    $(this).addClass('is-invalid');
                } else {
                    serialNumbers.push(serial);
                    $(this).removeClass('is-invalid');
                }
            });

            if (hasEmptySerials) {
                alert(`Please fill in all serial numbers for "${productName}" or remove empty fields.`);
                return;
            }

            if (serialNumbers.length === 0) {
                alert(`Please enter at least one serial number for "${productName}".`);
                return;
            }

            // Check for duplicate serial numbers
            const uniqueSerials = [...new Set(serialNumbers)];
            if (uniqueSerials.length !== serialNumbers.length) {
                alert(`Duplicate serial numbers detected for "${productName}". Please enter unique serial numbers.`);
                return;
            }

            // Update the quantity to match the number of serial numbers
            const quantityInput = $('input.product-quantity[data-product-id="' + productId + '"]');
            quantityInput.val(serialNumbers.length);

            // Save in global object
            serialNumbersData[productId] = serialNumbers;

            // Update the form with serial numbers
            $('#serialNumbersContainer').find(`.serial-group[data-product-id="${productId}"]`).remove();

            let serialInputsHtml = `<div class="serial-group" data-product-id="${productId}">`;
            serialNumbers.forEach(serial => {
                serialInputsHtml += `<input type="hidden" name="serial_numbers[${productId}][]" value="${serial}">`;
            });
            serialInputsHtml += `</div>`;

            $('#serialNumbersContainer').append(serialInputsHtml);

            $('#serialNumberModal').modal('hide');
            recalculateTotals();
        });

        // Form submission handler
        $('form').on('submit', function(e) {
            // Validate that at least one product is selected
            if ($('.productCheckbox:checked').length === 0) {
                e.preventDefault();
                alert("Please select at least one product.");
                return;
            }

            // Validate serial numbers for all selected products
            let isValid = true;
            $('.productCheckbox:checked').each(function() {
                const productId = $(this).val();
                const productName = $(this).closest('tr').find('td:eq(1)').text().trim();
                const quantity = parseInt($(`input.product-quantity[data-product-id="${productId}"]`).val(), 10) || 0;

                if (quantity > 0) {
                    const serials = serialNumbersData[productId] || [];
                    if (serials.length !== quantity) {
                        isValid = false;
                        alert(`Quantity (${quantity}) and serial numbers count (${serials.length}) don't match for product: ${productName}.`);
                        return false; // break out of each loop
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                return;
            }

            // Disable inputs for unselected products
            $('.productCheckbox').each(function() {
                if (!this.checked) {
                    $(this).closest('tr').find('input, select').prop('disabled', true);
                }
            });
        });

        // Recalculate totals whenever inputs change
        $(document).on('input', '.product-quantity, .price-per-unit-input, .cbm-input, .shipment-price-input, #exportWoodenCase, #sendToFoshanWarehouse, #agentPrice', function() {
            recalculateTotals();
        });

        // Recalculate totals when product selection changes
        $(document).on('change', '.productCheckbox', function() {
            recalculateTotals();
        });

        // Trigger the recalculation on page load
        recalculateTotals();

        // Format input values to three decimal places on blur
        $(document).on('blur', '.price-per-unit-input, .cbm-input, .shipment-price-input, #exportWoodenCase, #sendToFoshanWarehouse, #agentPrice, #agentCBM', function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                $(this).val(value.toFixed(3));
            }
        });

        // Function to recalculate totals
        function recalculateTotals() {
            let totalAmount = 0;
            let totalProductAmount = 0;
            let finalTotalAmount = 0;
            let totalCBM = 0;

            // Loop through selected products
            $('.productCheckbox:checked').each(function() {
                const row = $(this).closest('tr');

                // Get price, quantity, CBM, and shipment price from the row
                const price = parseFloat(row.find('.price-per-unit-input').val()) || 0;
                const quantity = parseInt(row.find('.product-quantity').val(), 10) || 0;
                const cbm = parseFloat(row.find('.cbm-input').val()) || 0;
                const shipmentPrice = parseFloat(row.find('input[name="shipment_price[]"]').val()) || 0;

                // Calculate the total for this row
                const rowTotal = (quantity * price) + cbm + shipmentPrice;
                totalAmount += rowTotal;
                totalProductAmount += rowTotal;
                finalTotalAmount += rowTotal;
                totalCBM += cbm;
            });

            // Add values of Export Wooden Case, Foshan Warehouse, and Agent Price to total
            const exportWoodenCase = parseFloat($('#exportWoodenCase').val()) || 0;
            const sendToFoshanWarehouse = parseFloat($('#sendToFoshanWarehouse').val()) || 0;
            const agentPrice = parseFloat($('#agentPrice').val()) || 0;

            totalAmount += exportWoodenCase + sendToFoshanWarehouse;
            finalTotalAmount += exportWoodenCase + sendToFoshanWarehouse + agentPrice;

            // Update the Agent CBM field
            $('#agentCBM').val(totalCBM.toFixed(3));

            // Update the Total Amount fields
            $('#totalProductAmount').val(totalProductAmount.toFixed(3));
            $('#totalAmount').val(totalAmount.toFixed(3));
            $('#finalTotalAmount').val(finalTotalAmount.toFixed(3));
        }
    });
</script>
<?= $this->endSection() ?>