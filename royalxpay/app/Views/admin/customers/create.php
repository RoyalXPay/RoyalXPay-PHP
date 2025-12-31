<!-- Modal for Adding Customer -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" role="dialog" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" action="<?= site_url('customers/save'); ?>" class="modal-content" id="addCustomerForm">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="newCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <?= view('admin/_topmessage'); ?>
                    <div id="addModalMsg" class="alert" style="display: none;"></div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter Full Name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Mobile No. <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" maxlength="10" required placeholder="Enter Mobile Number">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email ID</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email Address">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Emirates ID</label>
                        <input type="text" name="emirates_id" class="form-control" placeholder="Enter Emirates ID">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Address</label>
                        <textarea name="address" rows="2" class="form-control" placeholder="Enter Address"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Customer</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function () {
    // Add Customer Submit
    $('#addCustomerForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            dataType: 'json',
            success: function (res) {
                const msgBox = $('#addModalMsg');
                msgBox.show();

                if (res.status === 'success') {
                    msgBox
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(res.message);

                    setTimeout(() => {
                        $('#newCustomerModal').modal('hide');
                        msgBox.hide();
                        location.reload(); // Reload page or data table
                    }, 2000);
                } else {
                    msgBox
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html(res.message);
                }
            },
            error: function () {
                $('#addModalMsg')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .html('Something went wrong.')
                    .show();
            }
        });
    });
});
</script>
