<!-- Start Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="roleForm" autocomplete="off">
                    <input type="hidden" id="roleId" name="role_id">

                    <!-- Role Name -->
                    
                    <div class="mb-3">
    <label for="roleName" class="form-label">Merchant<span class="mandatory-field">*</span></label>
    <select class="form-select" id="roleName" name="role_name"  data-live-search="true" data-width="100%">
        <?php foreach ($merchantdetails as $merchantname): ?>
            <option value="<?= $merchantname['user_id'] . '|' . $merchantname['name'] ?>">
                <?= ucwords($merchantname['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
                            <small class="text-muted">You can select merchant.</small>

</div>

                    <!-- Permissions Dropdown -->
                    <div class="mb-3">
                        <label for="rolePermission" class="form-label">Permissions<span class="mandatory-field">*</span></label>
                        <select class="form-select" id="rolePermission" name="role_permission[]" multiple data-live-search="true" data-width="100%">
                            <?php foreach ($rolePermission as $permission): ?>
                                <option value="<?= $permission ?>">
                                    <?= ucwords($permission) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">You can select multiple permissions.</small>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the role"></textarea>
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