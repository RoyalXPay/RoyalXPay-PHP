<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sale</a></li>
                        <li class="breadcrumb-item active">Create Sale Contracts</li>
                    </ol>
                    <div class="page-title-right">
                        <a href="<?= site_url('sale-contracts'); ?>" class="btn btn-secondary waves-effect waves-light">
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
                            <form class="custom-validation" method='post' action="<?= site_url('sale-contracts/save') ?>" enctype='multipart/form-data'>
                                <?= \Config\Services::validation()->listErrors(); ?>
                                <?= csrf_field() ?>
                                <?= view('flash_messages'); ?>
                                <h4 class="card-title"><i class="fas fa-code-branch"></i> <?= isset($contractDetails) ? 'Edit' : 'Create' ?> Sale Contracts</h4>
                                <p class="card-subtitle mb-2 text-muted">Fill in the details below to <?= isset($contractDetails) ? 'edit' : 'create' ?> a Sale Contracts.</p>

                                <!-- Hidden contract ID and contract code (used for editing contracts) -->
                                <input type="hidden" id="contractId" name="contract_id" value="<?= $contractDetails['contract_id'] ?? ''; ?>">
                                <input type="hidden" id="contractCode" name="contract_code" value="<?= $contractDetails['contract_code'] ?? ''; ?>">

                                <!-- Customer Information: Select existing customer or add a new one -->
                                <div class="mb-3 row">
                                    <div class="col-10">
                                        <label for="userId" class="form-label">Select Customer or Add New Customer</label>
                                        <select class="form-select" id="userId" name="user_id" required>
                                            <option value="">Select a customer</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= htmlspecialchars($customer->user_id); ?>"
                                                    <?= ($customer->user_id == ($contractDetails["user_id"] ?? "")) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($customer->name) . ' (' . htmlspecialchars($customer->phone) . ')'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-2 d-flex align-items-end">
                                        <a href="<?= site_url('customers'); ?>" class="btn btn-primary w-100">Add</a>
                                    </div>
                                </div>

                                <!-- Product Table: List of products with attributes (name, size, color, etc.) that will be selected for the contract -->
                                <div class="mb-3">
                                    <table id="productTable" class="table table-bordered table-striped dt-responsive w-100">
                                        <thead>
                                            <tr>
                                                <th>Select</th>
                                                <th data-sortable="true" class="text-center">Source</th>
                                                <th data-sortable="true" class="text-center">Product Name</th>
                                                <th data-sortable="true" class="text-center">Serial Number</th>
                                                <th data-sortable="true" class="text-center">Size</th>
                                                <th data-sortable="true" class="text-center">Colour</th>
                                                <th data-sortable="true" class="text-center">Glass Type</th>
                                                <th data-sortable="true" class="text-center">Touch/ Non Touch</th>
                                                <th data-sortable="true" class="text-center">Quantity</th>
                                                <th data-sortable="true" class="text-center">Price</th>
                                                <th data-sortable="true" class="text-center">Discount</th>
                                                <th data-sortable="false" class="text-center">Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product):
                                                $productId = $product->unique_id;
                                                $isPurchase = ($product->master_product_type ?? '') === 'purchase';
                                                $serialNumber = $isPurchase ? ($product->serial_number ?? 'N/A') : 'N/A';

                                                // Check if the product is part of the contract
                                                $isChecked = isset($contractDetails['contractProducts'][$productId]);

                                                // Get the saved quantity and discount, fallback to empty if not in contract
                                                $quantity = $isChecked ? $contractDetails['contractProducts'][$productId]['quantity'] : ($isPurchase ? 1 : '');
                                                $discount = $isChecked ? $contractDetails['contractProducts'][$productId]['discount'] : '';
                                                $quantityReadonly = $isPurchase ? 'readonly' : '';

                                                $saleProductImages = explode(',', $product->product_images);
                                                $imageUrl = base_url('uploads/products/' . $saleProductImages[0]);
                                            ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input productCheckbox"
                                                                name="productIds[<?= $productId ?>]" value="<?= $productId ?>" <?= $isChecked ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                                                            <input type="hidden" name="source[<?= $productId ?>]" value="<?= esc($product->product_source) ?>">
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><?= esc($product->product_source) ?></td>
                                                    <td class="text-center"><?= esc($product->product_name) ?></td>
                                                    <td class="text-center"><?= esc($serialNumber) ?></td>
                                                    <td class="text-center"><?= esc($product->size_in_inches . ' inches') ?></td>
                                                    <td class="text-center"><?= esc($product->color_name) ?></td>
                                                    <td class="text-center"><?= esc($product->glass_name) ?></td>
                                                    <td class="text-center"><?= esc($product->touch_name) ?></td>
                                                    <td class="text-center">
                                                        <input type="number" class="form-control quantity-input" 
                                                        name="quantity[<?= $productId ?>]" placeholder="Quantity" value="<?= $quantity ?>" min="1" style="width: 100px;" <?= $quantityReadonly ?>>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= esc($product->product_price ?? '0.000') ?>
                                                        <input type="hidden" name="price_per_unit[<?= $productId ?>]" value="<?= $product->product_price ?? 0 ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" class="form-control discount-input" 
                                                        name="discount[<?= $productId ?>]" min="0" max="100" step="0.01" placeholder="Discount (%)" style="width: 100px;" value="<?= $discount ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <img src="<?= $imageUrl ?>" alt="<?= esc($product->product_name) ?>" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Dynamic Calculations: Show total amount after discounts -->
                                <div class="mb-3">
                                    <label for="totalAmount" class="form-label">Total Amount After Discounts</label>
                                    <input type="text" class="form-control" id="totalAmount" name="total_amount" value="<?= $contractDetails['total_price'] ?? ''; ?>" readonly placeholder="Total amount will appear here">
                                </div>

                                <!-- Discount Amount: Display the discount applied -->
                                <div class="mb-3">
                                    <label for="discountAmount" class="form-label">Discount Amount</label>
                                    <input type="text" class="form-control" id="discountAmount" name="discount_amount" readonly placeholder="Discount amount will appear here">
                                </div>

                                <!-- VAT: Display VAT (5%) applied on the contract -->
                                <div class="mb-3">
                                    <label for="vat" class="form-label">VAT (5%)</label>
                                    <input type="text" class="form-control" id="vat" name="vat" value="<?= $contractDetails['vat'] ?? ''; ?>" readonly placeholder="VAT will appear here">
                                </div>

                                <!-- Final Payable Amount: Show the final amount after VAT -->
                                <div class="mb-3">
                                    <label for="finalPayableAmount" class="form-label">Final Payable Amount</label>
                                    <input type="text" class="form-control" id="finalPayableAmount" name="final_payable_price" value="<?= $contractDetails['final_payable_price'] ?? ''; ?>" readonly placeholder="Final Amount will appear here">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Citylight Information</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_logo" id="citylightLogo" <?= (isset($contractDetails['citylight_logo']) && $contractDetails['citylight_logo'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightLogo">Citylight Logo</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_address" id="citylightAddress" <?= (isset($contractDetails['citylight_address']) && $contractDetails['citylight_address'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightAddress">Citylight Address</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_contact_details" id="citylightContact" <?= (isset($contractDetails['citylight_contact_details']) && $contractDetails['citylight_contact_details'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightContact">Citylight Contact Details</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_email" id="citylightEmail" <?= (isset($contractDetails['citylight_email']) && $contractDetails['citylight_email'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightEmail">Citylight Email</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="citylight_website" id="citylightWebsite" <?= (isset($contractDetails['citylight_website']) && $contractDetails['citylight_website'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="citylightWebsite">Citylight Website</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="bank_details" id="bankDetails" <?= (isset($contractDetails['bank_details']) && $contractDetails['bank_details'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="bankDetails">Bank Details</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="return_policies" id="returnPolicies" <?= (isset($contractDetails['return_policies']) && $contractDetails['return_policies'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="returnPolicies">Return Policies</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="signature" id="signature" <?= (isset($contractDetails['signature']) && $contractDetails['signature'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="signature">Signature</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="stamp" id="stamp" <?= (isset($contractDetails['stamp']) && $contractDetails['stamp'] == 1) ? "checked" : "checked"; ?>>
                                        <label class="form-check-label" for="stamp">Stamp</label>
                                    </div>
                                </div>

                                <!-- Delivery Time: Select delivery time for the sale -->
                                <div class="mb-3">
                                    <label for="deliveryTimes" class="form-label">Delivery Time for Sale</label>
                                    <select id="deliveryTimes" name="delivery_time" class="form-select" placeholder="Select delivery time for sale">
                                        <option value="">Select Delivery Time for Sale</option>
                                        <?php $deliveryTime = $contractDetails["delivery_time"] ?? ""; ?>
                                        <?php foreach ($deliveryTimes as $deliveryTimeDetails): ?>
                                            <option value="<?= esc($deliveryTimeDetails->id); ?>" <?= ($deliveryTimeDetails->id == $deliveryTime) ? "selected" : ""; ?>><?= esc($deliveryTimeDetails->title); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Terms and Conditions: Select the applicable terms and conditions for the contract -->
                                <div class="mb-3">
                                    <label for="termsConditions" class="form-label">Select Terms and Conditions</label>
                                    <select id="termsConditions" name="terms_conditions" class="form-select" placeholder="Select terms and conditions">
                                        <option value="">Select Terms and Conditions</option>
                                        <?php $termsConditions = $contractDetails['terms_conditions'] ?? ""; ?>
                                        <?php foreach ($termsAndConditions as $term): ?>
                                            <option value="<?= esc($term->id); ?>" <?= ($term->id == $termsConditions) ? "selected" : ""; ?>><?= esc($term->title); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Notes: Additional notes for the sale contract -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any additional notes here"><?= $contractDetails["notes"] ?? ''; ?></textarea>
                                </div>

                                <!-- Modal Footer: Buttons to cancel or save the contract -->
                                <div class="text-end">
                                    <a href="<?= site_url('sale-contracts'); ?>" class="btn btn-outline-secondary">Cancel</a>
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
    });

    recalculateTotals();
    attachCalculationListeners();

    // Function to attach calculation listeners
    function attachCalculationListeners() {
        // Listener for quantity or discount input changes
        $(document).on('input', '.quantity-input, .discount-input', function() {
            recalculateTotals();
        });

        // Listener for product selection changes
        $(document).on('change', '.productCheckbox', function() {
            recalculateTotals();
        });

    }

    // Function to recalculate totals
    function recalculateTotals() {

        let totalAmount = 0;
        let discountAmount = 0;

        $('.productCheckbox:checked').each(function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            const price = parseFloat(row.find('input[name^="price_per_unit"]').val()) || 0;
            const discountInput = parseFloat(row.find('.discount-input').val()) || 0;

            // Calculate the total price before discount (quantity * price)
            const productTotal = quantity * price;

            // Apply discount to the product total
            const discountForProduct = (productTotal * discountInput) / 100;

            // Subtract the discount from the product total to get the discounted price
            const discountedPrice = productTotal - discountForProduct;

            totalAmount += discountedPrice;
            discountAmount += discountForProduct;
        });

        // Calculate VAT (5%) on the discounted total
        const vat = totalAmount * 0.05;

        // Calculate the final payable amount after applying VAT
        const finalPayableAmount = totalAmount + vat;

        // Update the fields with calculated values
        $('#totalAmount').val(totalAmount.toFixed(3));
        $('#discountAmount').val(discountAmount.toFixed(3));
        $('#vat').val(vat.toFixed(3));
        $('#finalPayableAmount').val(finalPayableAmount.toFixed(3));
    }
</script>
<?= $this->endSection() ?>