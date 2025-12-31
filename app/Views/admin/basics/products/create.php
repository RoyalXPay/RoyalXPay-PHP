<!-- Start Modal -->
<div class="modal fade" id="newProductTypeModal" tabindex="-1" aria-labelledby="newProductTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newProductTypeModalLabel">Add Product Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="productTypeForm" autocomplete="off">
                    <input type="hidden" id="productTypeId" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Enter product type name">
                    </div>

                    <!-- Description -->
                    <!-- <div class="mb-3">
                        <label for="description" class="form-label">Description<span class="mandatory-field">*</span></label>
                        <textarea class="form-control" id="description" name="description" required placeholder="Enter product type description"></textarea>
                    </div> -->

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div><!-- end modal body -->
        </div><!-- end modal-content -->
    </div><!-- end modal-dialog -->
</div><!-- end Modal -->