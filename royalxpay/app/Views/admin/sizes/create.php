<!-- Start Modal -->
<div class="modal fade" id="newSizeModal" tabindex="-1" aria-labelledby="newSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSizeModalLabel">Add Size</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="sizeForm" autocomplete="off">

                    <input type="hidden" id="sizeId" name="size_id">

                    <!-- Size in Inches -->
                    <div class="mb-3">
                        <label for="size_in_inches" class="form-label">Size in Inches<span class="mandatory-field">*</span></label> <!-- Changed label -->
                        <input type="text" class="form-control" id="size_in_inches" name="size_in_inches" required placeholder="Enter size in inches"> <!-- Changed input name and ID -->
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
