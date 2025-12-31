<!-- Start Modal -->
<div class="modal fade" id="submoduleModal" tabindex="-1" aria-labelledby="submoduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submoduleModalLabel">Add Submodule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="submoduleForm" autocomplete="off">
                    <input type="hidden" id="submoduleId" name="submodule_id">

                    <!-- Module Selection (Dropdown for Module) -->
                    <div class="mb-3">
                        <label for="moduleSelect" class="form-label">Select Module<span class="mandatory-field">*</span></label>
                        <select class="form-select" id="moduleSelect" name="module_id" required>
                            <option value="">Select Module</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?= esc($module['module_id']); ?>"><?= esc($module['module_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Submodule Name -->
                    <div class="mb-3">
                        <label for="submoduleName" class="form-label">Submodule Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="submoduleName" name="submodule_name" required placeholder="Enter submodule name">
                    </div>

                    <!-- Submodule Description -->
                    <div class="mb-3">
                        <label for="submoduleDescription" class="form-label">Submodule Description<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="submoduleDescription" name="submodule_description" required placeholder="Enter submodule description">
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