<!-- Modal for Add Payment -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" action="<?= site_url('customers/add-payment'); ?>" class="modal-content" id="addPaymentForm">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
<div id="paymentModalMsg" class="alert" style="display: none;"></div>

                <!-- Hidden ID field for update -->
                <input type="hidden" name="id" id="payment_id"> <!-- This is critical -->

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="paid_amount" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Payment Mode <span class="text-danger">*</span></label>
                        <select name="mode_of_payment" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="UPI">UPI</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit Payment</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {
    $('#addPaymentForm').on('submit', function (e) {
        e.preventDefault(); // prevent normal form submission

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            dataType: 'json',
            success: function (res) {
                const msgBox = $('#paymentModalMsg');
                msgBox.show();

                if (res.status === 'success') {
                    msgBox
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(res.message);

                    setTimeout(() => {
                        $('#addPaymentModal').modal('hide');
                        msgBox.hide();
                        location.reload();
                    }, 2000);
                } else {
                    msgBox
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html(res.message);
                }
            },
            error: function () {
                $('#paymentModalMsg')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .html('Something went wrong.')
                    .show();
            }
        });
    });
});

    </script>