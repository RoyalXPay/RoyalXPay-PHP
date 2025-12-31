<!-- Start Modal -->
<div class="modal fade" id="newEmployeeModal" tabindex="-1" aria-labelledby="newEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEmployeeModalLabel">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="employeeForm" autocomplete="off" onsubmit="return validatePassword()">
                    <input type="hidden" id="employeeId" name="employee_id">

                    <div class="mb-3">
                        <label for="name" class="form-label">Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Enter name">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password<span class="mandatory-field">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">👁</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password<span class="mandatory-field">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required placeholder="Confirm password" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">👁</button>
                        </div>
                        <div class="text-danger" id="passwordError" style="display: none;">Passwords do not match!</div>
                    </div>

                    <div class="mb-3">
                        <label for="employeeStatus" class="form-label">Status<span class="mandatory-field">*</span></label>
                        <select id="employeeStatus" class="form-control" name="status" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permissionView" name="permissions[]" value="view" style="transform: scale(1.5);">
                                <label class="form-check-label" for="permissionView">View</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permissionAdd" name="permissions[]" value="add" style="transform: scale(1.5);">
                                <label class="form-check-label" for="permissionAdd">Add</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permissionEdit" name="permissions[]" value="edit" style="transform: scale(1.5);">
                                <label class="form-check-label" for="permissionEdit">Edit</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permissionDelete" name="permissions[]" value="delete" style="transform: scale(1.5);">
                                <label class="form-check-label" for="permissionDelete">Delete</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Module Access</label>
                        <div class="d-flex gap-3 flex-wrap">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="customers" name="modules[]" value="customers">
                                <label class="form-check-label" for="customers">Customers</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="basics" name="modules[]" value="basics">
                                <label class="form-check-label" for="basics">Basics</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="products" name="modules[]" value="products">
                                <label class="form-check-label" for="products">Products</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="purchases" name="modules[]" value="purchases">
                                <label class="form-check-label" for="purchases">Purchases</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rent" name="modules[]" value="rent">
                                <label class="form-check-label" for="rent">Rent</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sale" name="modules[]" value="sale">
                                <label class="form-check-label" for="sale">Sale</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="orders" name="modules[]" value="orders">
                                <label class="form-check-label" for="orders">Website Orders</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="company" name="modules[]" value="company">
                                <label class="form-check-label" for="company">Company</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="employees" name="modules[]" value="employees">
                                <label class="form-check-label" for="employees">Employees</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="reports" name="modules[]" value="reports">
                                <label class="form-check-label" for="reports">Reports</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="feedback" name="modules[]" value="feedback">
                                <label class="form-check-label" for="feedback">Feedback</label>
                            </div>

                        </div>
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