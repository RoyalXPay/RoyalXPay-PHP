<!-- Start Modal -->
<div class="modal fade" id="newTouchTypeModal" tabindex="-1" aria-labelledby="newTouchTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newTouchTypeModalLabel">Add Touch Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="touchTypeForm" autocomplete="off">
                    <input type="hidden" id="touchTypeId" name="touch_id">

                    <!-- Name (Touch Type Name) -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Touch Type Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Enter touch type name">
                    </div>

                    <!-- Description -->
                    <!-- <div class="mb-3">
                        <label for="description" class="form-label">Description<span class="mandatory-field">*</span></label>
                        <textarea class="form-control" id="description" name="description" required placeholder="Enter touch type description"></textarea>
                    </div> -->

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
