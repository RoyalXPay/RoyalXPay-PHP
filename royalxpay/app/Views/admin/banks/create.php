<!-- Start Modal -->
<div class="modal fade" id="bankDetailsModal" tabindex="-1" aria-labelledby="bankDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankDetailsModalLabel">Add Bank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="bankDetailsForm" autocomplete="off">
                    <input type="hidden" id="bankId" name="bank_id">

                    <!-- Bank Name -->
                    <div class="mb-3">
                        <label for="bankName" class="form-label">Bank Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="bankName" name="bank_name" required placeholder="Enter bank name">
                    </div>

                    <!-- Account Holder Name -->
                    <div class="mb-3">
                        <label for="accountHolderName" class="form-label">Account Holder Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="accountHolderName" name="account_holder" required placeholder="Enter account holder name">
                    </div>

                    <!-- Account Number -->
                    <div class="mb-3">
                        <label for="accountNumber" class="form-label">Account Number<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="accountNumber" name="account_number" required placeholder="Enter account number">
                    </div>

                    <!-- IFSC Code -->
                    <div class="mb-3">
                        <label for="ifscCode" class="form-label">IFSC Code<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="ifscCode" name="ifsc_code" required placeholder="Enter IFSC code">
                    </div>

                    <!-- SWIFT Code -->
                    <div class="mb-3">
                        <label for="swiftCode" class="form-label">SWIFT Code<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="swiftCode" name="swift_code" required placeholder="Enter SWIFT code">
                    </div>

                    <!-- Branch Name -->
                    <div class="mb-3">
                        <label for="branchName" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="branchName" name="branch_name" placeholder="Enter branch name">
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