<!-- Start Modal -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCustomerModalLabel">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="customerForm" autocomplete="off">
                    <input type="hidden" id="customerId" name="customer_id">

                    <div class="mb-3">
                        <label for="name" class="form-label">Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Enter name">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone<span class="mandatory-field">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required placeholder="Enter phone number">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                    </div>

                    <div class="mb-3">
                        <label for="altMobileNumber" class="form-label">Alternate Mobile Number</label>
                        <input type="tel" class="form-control" id="altMobileNumber" name="alt_mobile_number" placeholder="Enter alternate mobile number">
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" class="form-control" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="customerStatus" class="form-label">Status</label>
                        <select id="customerStatus" class="form-control" name="status">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" placeholder="Enter Note"></textarea>
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