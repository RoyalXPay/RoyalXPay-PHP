<!-- Start Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="permissionForm" autocomplete="off">
                    <input type="hidden" id="permissionId" name="id">

                    <!-- Role Selection (Dropdown for Role) -->
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role<span class="mandatory-field">*</span></label>
                        <select class="form-select" id="roleSelect" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= esc($role['role_id']); ?>"><?= esc($role['role_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Module Selection (Dropdown for Module) -->
                    <div class="mb-3">
                        <label for="moduleSelect" class="form-label">Select Module<span class="mandatory-field">*</span></label>
                        <select class="form-select selectpicker" id="moduleSelect" name="module_id[]" multiple data-live-search="true" required onchange="loadSubmodules()" data-width="100%">
                            <?php foreach ($modules as $module): ?>
                                <option value="<?= esc($module['module_id']); ?>"><?= esc($module['module_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Submodule Selection (Multi-Select) -->
                    <div class="mb-3">
                        <label for="submoduleSelect" class="form-label">Select Submodule<span class="mandatory-field">*</span></label>
                        <select class="form-select selectpicker" id="submoduleSelect" name="submodule_id[]" multiple data-live-search="true" required data-width="100%">
                        </select>
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