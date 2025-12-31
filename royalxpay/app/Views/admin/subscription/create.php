<!-- Start Modal -->
<div class="modal fade" id="newSubscriptionPackageModal" tabindex="-1" aria-labelledby="newSubscriptionPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSubscriptionPackageModalLabel">Add Subscription Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="subscriptionPackageForm" autocomplete="off">
                    <input type="hidden" id="subscriptionPackageId" name="id">

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title<span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="Enter subscription package title">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter subscription package description"></textarea>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price<span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" required placeholder="Enter price">
                    </div>

                    <!-- Duration -->
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (months)<span class="mandatory-field">*</span></label>
                        <select class="form-control" id="duration" name="duration" required>
                            <option value="" disabled selected>Select Duration</option>
                            <?php foreach ($durations as $key => $month): ?>
                                <option value="<?= $key ?>" <?= (isset($subscription) && $subscription->duration == $key) ? 'selected' : '' ?>>
                                    <?= $month ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status<span class="mandatory-field">*</span></label>
                        <select class="form-select" id="subscriptionStatus" name="status" required>
                            <option value="" disabled selected>Select status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Thumbnail (Image Upload or URL) -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Thumbnail (Image)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event, 'imagePreview')">

                        <!-- Image Preview inside the Modal -->
                        <div class="mt-3 text-center" id="imagePreviewWrapper" style="display:none;">
                            <img id="imagePreview" class="img-fluid" alt="Image Preview" src="" style="max-height: 300px; object-fit: contain;">
                        </div>

                        <small class="form-text text-muted">You can upload an image or provide a URL for the thumbnail.</small>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div><!-- end modal body -->
        </div><!-- end modal-content -->
    </div><!-- end modal-dialog -->
</div><!-- end Modal -->

<script>
    // Function to preview image in the modal
    function previewImage(event, previewId) {
        const file = event.target.files[0];
        const reader = new FileReader();
        const imgPreview = document.getElementById(previewId);
        const previewWrapper = document.getElementById('imagePreviewWrapper');

        reader.onload = function() {
            imgPreview.src = reader.result;
            previewWrapper.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, reset the preview
            imgPreview.src = '';
            previewWrapper.style.display = 'none';
        }
    }

    // To initialize the preview on page load for existing images (for Edit)
    window.onload = function() {
        const imgPreview = document.getElementById('imagePreview');
        const previewWrapper = document.getElementById('imagePreviewWrapper');
        const imageInput = document.getElementById('image');

        // If an image URL exists (for edit scenario), show the preview
        if (imgPreview.src) {
            previewWrapper.style.display = 'block';
        }

        // If there is a file input value, show the preview (for edit, where the image was already uploaded)
        if (imageInput.files.length > 0) {
            previewWrapper.style.display = 'block';
        }

        // Check if an existing image should be removed in edit mode
        const existingImage = false;
        if (!existingImage) {
            previewWrapper.style.display = 'none';
        }
    };
</script>