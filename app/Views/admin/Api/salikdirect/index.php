<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-type-btn.active {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd !important;
    }
    .error-message {
        color: red;
        font-size: 13px;
        display: none;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Left: Logo + Title -->
        <div class="d-flex align-items-center">
            <img src="<?= base_url('assets/images/salik.png') ?>" alt="Salik Logo" style="height:61px; margin-right:10px;">
            <h4 class="card-title m-0">Salik Direct</h4>
        </div>

        <!-- Right: Action Buttons -->
        <div class="d-flex gap-2">
            <a href="<?= site_url('api/salikdirect/reports') ?>" class="btn btn-info">Salik Direct Payment Reports</a>
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
        </div>
    </div>

        <div class="card">
            <div class="card-body">
                <form id="salikdirect-payment-form">
                    <div class="row mb-3">
                        <label>Select Search Type</label>
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="AccountID">Account Number</button>
                        </div>
                    </div>

                    <div class="row mb-3" id="inputFields" style="display:none;">
                        <div class="col-md-4">
                            <label>Account Number</label>
                            <input type="text" id="accountNumber" name="accountNumber" class="form-control">
                            <small id="accountError" class="error-message"></small>
                        </div>
                        <div class="col-md-4">
                            <label>Account PIN</label>
                            <input type="text" id="accountPin" name="accountPin" class="form-control">
                            <small id="pinError" class="error-message"></small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 d-flex">
                            <button type="button" id="payBtn" class="btn btn-success w-100" disabled>Check Balance</button>
                        </div>
                    </div>

                    <input type="hidden" id="transaction_id">
                    <input type="hidden" id="customer_name" value="Web Portal User">

                    <div id="balanceResultSection" class="mt-4" style="display:none;">
                        <h5>Bill Details</h5>
                        <table class="table table-bordered">
                            <tbody id="balanceDetails"></tbody>
                        </table>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="continueBtn">Pay</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let selectedSearchType = '';

    // When a search type is clicked (e.g., Account Number)
$('.search-type-btn').on('click', function () {
    $('.search-type-btn').removeClass('active');
    $(this).addClass('active');

    selectedSearchType = $(this).data('type');

    // Show input fields when a type is selected
    $('#inputFields').show();

    // Reset previous values and errors
    $('#accountNumber').val('');
    $('#accountPin').val('');
    $('#accountError').hide();
    $('#pinError').hide();

    // Disable pay button initially
    $('#payBtn').prop('disabled', true);
});


    function validateInput(value, type) {
    const accountErrorEl = $('#accountError');
    accountErrorEl.hide();

    if (!value) {
        accountErrorEl.text('Account number is required.').show();
        return false;
    }

    if (type === 'AccountID') {
        if (value.length < 4 || value.length > 10) {
            accountErrorEl.text('Account number must be between 4 and 10 characters.').show();
            return false;
        }
    }

    return true;
}
    function validatePin(pin) {
        const pinErrorEl = $('#pinError');
        pinErrorEl.hide();

        if (!pin) {
            pinErrorEl.text('PIN is required.').show();
            return false;
        }

        if (!/^\d{4,6}$/.test(pin)) {
            pinErrorEl.text('PIN must be 4 to 6 digits.').show();
            return false;
        }

        return true;
    }

    function togglePayButton() {
        const accountNumber = $('#accountNumber').val().trim();
        const accountPin = $('#accountPin').val().trim();

        const isAccountValid = selectedSearchType && validateInput(accountNumber, selectedSearchType);
        const isPinValid = validatePin(accountPin);

        $('#payBtn').prop('disabled', !(isAccountValid && isPinValid));
    }

   
    
    $('#accountNumber, #accountPin').on('input', function () {
        togglePayButton();
    });

    
   $('#payBtn').click(async function () {
const value = $('#accountNumber').val().trim();
    const pin = $('#accountPin').val().trim();   // ✅ define pin here

        try {
            const balanceRes = await fetch("<?= site_url('api/salikdirect/fetch-bill') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    account_number: value,
                        account_pin: pin,   // ✅ Added PIN here
                    req_type: selectedSearchType
                })
            });

            const balance = await balanceRes.json();
            if (!balance.status) {
                Swal.fire('Error', balance.message || 'Balance fetch failed.', 'error');
                return;
            }

            // Inside #payBtn click -> after parsing balance response
let html = '';
const data = balance.data;

// Show all top-level fields first
for (const key in data) {
    if (key !== 'amount') {   // skip amount here, we'll handle separately
        html += `<tr><th>${key}</th><td>${data[key]}</td></tr>`;
    }
}

// Handle amount as editable field
if (data.amount) {
    const commission = 2;
    const balanceAmount = parseFloat(data.amount || 0);
    const finalAmount = (balanceAmount + commission).toFixed(2);

    html += `<tr>
        <th>Amount</th>
        <td><input type="number" min="0" step="0.01" id="editableBalance" class="form-control" value="${balanceAmount.toFixed(2)}" /></td>
    </tr>`;
    html += `<tr><th>Commission</th><td>${commission.toFixed(2)} AED</td></tr>`;
    html += `<tr><th>Final Payable Amount (AED)</th>
        <td><input type="text" id="finalAmount" class="form-control" value="${finalAmount} AED" readonly /></td>
    </tr>`;

    // set payload for payment
    $('#continueBtn').text(`Pay Amount: ${finalAmount} AED`);
    $('#continueBtn').data('payload', {
        account_number: data.accountNumber,
        account_pin: $('#accountPin').val().trim(),
        amount_without_commission: balanceAmount.toFixed(2),
        amount_with_commission: finalAmount,
        provider_transaction_id: data.providerTransactionId,
        cust_name: data.custName,
        oid: balance.oid,
        secure_sign: balance.secure_sign,
        timestamp: balance.timestamp
    });
}

// Inject into table
$('#balanceDetails').html(html);
$('#balanceResultSection').show();

// Enable editing
$('#editableBalance').on('input', function () {
    const newBalance = parseFloat($(this).val()) || 0;
    const commission = 2;
    const finalPayable = (newBalance + commission).toFixed(2);

    $('#finalAmount').val(`${finalPayable} AED`);
    $('#continueBtn').text(`Pay Amount: ${finalPayable} AED`);

    const currentPayload = $('#continueBtn').data('payload') || {};
    currentPayload.amount_without_commission = newBalance.toFixed(2);
    currentPayload.amount_with_commission = finalPayable;
    $('#continueBtn').data('payload', currentPayload);
});
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

    // Payment
    $('#continueBtn').click(async function () {
        const payload = $(this).data('payload');
                    if (!payload || !payload.account_number || !payload.amount_with_commission) {
                Swal.fire('Error', 'Missing payment details. Please re-check balance.', 'error');
                return;
            }

        const { value: mobile } = await Swal.fire({
            title: 'Enter Customer Mobile No.',
            input: 'tel',
            inputLabel: 'Mobile Number',
            inputPlaceholder: '05XXXXXXXX',
            inputAttributes: { maxlength: 15 },
            confirmButtonText: 'Continue to Pay',
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) return 'Mobile number is required';
                if (!/^\d{10,15}$/.test(value)) return 'Enter a valid mobile number (10-15 digits)';
                return null;
            }
        });

        if (!mobile) return;

        payload.customer_mobile = mobile;

        try {

            
            const payRes = await fetch("<?= site_url('api/salikdirect/make-payment') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const payResult = await payRes.json();
            if (payResult.status) {
                Swal.fire('Success', 'Transaction successful!', 'success');
                setTimeout(() => { window.location.href = "salikdirect/reports"; }, 2000);
            } else {
                Swal.fire('Failed', payResult.message || 'Transaction failed.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

</script>

<?= $this->endSection() ?>
