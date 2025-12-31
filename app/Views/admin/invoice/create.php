<!-- Start Modal -->
<div class="modal fade" id="newInvoiceModal" tabindex="-1" aria-labelledby="newInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newInvoiceModalLabel">Add Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="invoiceForm" autocomplete="off">
                    <input type="hidden" id="invoiceId" name="invoice_id">

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="productName" name="product_name" required placeholder="Enter product name" onclick="showProductDetails()">
                    </div>

                    <div class="mb-3">
                        <label for="companyName" class="form-label">Company Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="companyName" name="company_name" required placeholder="Enter company name" onclick="showCompanyDetails()">
                    </div>

                    <div class="mb-3">
                        <label for="forRent" class="form-label">For Rent / For Sale<span class="mandatory-field">*</span></label>
                        <select id="forRent" class="form-control" name="for_rent" required>
                            <option value="">Select Option</option>
                            <option value="rent">For Rent</option>
                            <option value="sale">For Sale</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="invoiceDate" class="form-label">Invoice Date & Time<span class="mandatory-field">*</span></label>
                        <input type="datetime-local" class="form-control" id="invoiceDate" name="invoice_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required placeholder="Enter quantity" min="1">
                    </div>

                    <div class="mb-3">
                        <label for="amountPerUnit" class="form-label">Amount per Unit ($)<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="amountPerUnit" name="amount_per_unit" required placeholder="Enter amount" min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="paidAmount" class="form-label">Paid Amount ($)<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="paidAmount" name="paid_amount" required placeholder="Enter paid amount" min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="vat" class="form-label">VAT (5%) ($)<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="vat" name="vat" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="totalPaid" class="form-label">Total Paid Amount ($)<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="totalPaid" name="total_paid" readonly>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="bankDetails" name="bank_details">
                        <label class="form-check-label" for="bankDetails">Bank Details</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="citylightLogo" name="citylight_logo">
                        <label class="form-check-label" for="citylightLogo">Citylight Logo</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="citylightAddress" name="citylight_address">
                        <label class="form-check-label" for="citylightAddress">Citylight Address</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="citylightWebsite" name="citylight_website">
                        <label class="form-check-label" for="citylightWebsite">Citylight Website</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="citylightEmail" name="citylight_email">
                        <label class="form-check-label" for="citylightEmail">Citylight Email Address</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="citylightContact" name="citylight_contact">
                        <label class="form-check-label" for="citylightContact">Citylight Contact Details</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="stamp" name="stamp">
                        <label class="form-check-label" for="stamp">Stamp</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="signature" name="signature">
                        <label class="form-check-label" for="signature">Signature</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="returnPolicies" name="return_policies">
                        <label class="form-check-label" for="returnPolicies">Return Policies</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="termsConditions" name="terms_conditions">
                        <label class="form-check-label" for="termsConditions">Terms and Conditions</label>
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
    // Add event listeners to calculate VAT and Total Paid Amount
    document.getElementById('paidAmount').addEventListener('input', calculateTotal);
    document.getElementById('amountPerUnit').addEventListener('input', calculateTotal);
    document.getElementById('quantity').addEventListener('input', calculateTotal);

    function calculateTotal() {
        const amountPerUnit = parseFloat(document.getElementById('amountPerUnit').value) || 0;
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;

        const vat = (amountPerUnit * quantity * 0.05).toFixed(2);
        document.getElementById('vat').value = vat;

        const totalPaid = (paidAmount + parseFloat(vat)).toFixed(2);
        document.getElementById('totalPaid').value = totalPaid;
    }

    // Functions to show product and company details (you need to implement these)
    function showProductDetails() {
        // Logic to display product details
    }

    function showCompanyDetails() {
        // Logic to display company details
    }
</script>