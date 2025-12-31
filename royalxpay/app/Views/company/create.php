<!-- Start Modal -->
<div class="modal fade" id="newCompanyModal" tabindex="-1" aria-labelledby="newCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCompanyModalLabel">Add Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="companyForm" autocomplete="off">
                    <input type="hidden" id="companyId" name="company_id">

                    <div class="mb-3">
                        <label for="companyName" class="form-label">Company Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="companyName" name="company_name" required placeholder="Enter company name">
                    </div>

                    <div class="mb-3">
                        <label for="mobileNumber" class="form-label">Mobile Number<span class="mandatory-field">*</span></label>
                        <input type="tel" class="form-control" id="mobileNumber" name="mobile_number" required placeholder="Enter mobile number">
                    </div>

                    <div class="mb-3">
                        <label for="altMobileNumber" class="form-label">Alternate Mobile Number</label>
                        <input type="tel" class="form-control" id="altMobileNumber" name="alt_mobile_number" placeholder="Enter alternate mobile number">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                    </div>

                    <div class="mb-3">
                        <label for="companyStatus" class="form-label">Status</label>
                        <select id="companyStatus" class="form-control" name="status">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" placeholder="Enter address"></textarea>
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
