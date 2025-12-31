<!-- Start Modal -->
<div class="modal fade" id="newResolutionModal" tabindex="-1" aria-labelledby="newResolutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newResolutionModalLabel">Add Resolution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="resolutionForm" autocomplete="off">
                    <input type="hidden" id="resolutionId" name="resolution_id">

                    <!-- resolution_type -->
                    <div class="mb-3">
                        <label for="resolutionType" class="form-label">Resolution</label>
                        <textarea class="form-control" id="resolutionType" name="resolution_type" placeholder="Enter Resolution Type"></textarea>
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