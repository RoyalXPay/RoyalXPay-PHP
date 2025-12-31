<!-- Start Modal -->
<div class="modal fade" id="newDeliveryTimeModal" tabindex="-1" aria-labelledby="newDeliveryTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newDeliveryTimeModalLabel">Add Delivery Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="deliveryTimeForm" autocomplete="off">
                    <input type="hidden" id="deliveryTimeId" name="id">

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="Enter title for delivery time">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description<span class="mandatory-field">*</span></label>
                        <textarea class="form-control" id="description" name="description" required placeholder="Enter description for delivery time"></textarea>
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