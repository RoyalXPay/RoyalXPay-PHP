<!-- Modal for Editing Customer -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" action="<?= site_url('customers/save'); ?>" class="modal-content" id="editCustomerForm">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <?= view('admin/_topmessage'); ?>
                <div id="editModalMsg" class="alert" style="display: none;"></div>

                <input type="hidden" name="id" id="edit_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Mobile No. <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" id="edit_phone" class="form-control" pattern="[0-9]{10}" maxlength="10" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email ID</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Emirates ID</label>
                        <input type="text" name="emirates_id" id="edit_emirates_id" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Address</label>
                        <textarea name="address" id="edit_address" rows="2" class="form-control"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Customer</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
