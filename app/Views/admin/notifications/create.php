<!-- Start Modal -->
<div class="modal fade" id="newNotificationModal" tabindex="-1" aria-labelledby="newNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newNotificationModalLabel">Add New Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="notificationForm" autocomplete="off">
                    <input type="hidden" id="notificationId" name="notification_id">

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="Enter notification title">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter notification description"></textarea>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Upload Attachment</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*">
                        <div id="attachmentPreviewContainer" class="mt-3" style="display:none;">
                            <h6>Preview:</h6>
                            <img id="attachmentPreview" src="" alt="Attachment Preview" class="img-fluid" style="max-width: 100%; height: auto;">
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