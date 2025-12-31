<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-type-btn.active {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd !important;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <!-- Left: Logo + Title -->
    <div class="d-flex align-items-center">
        <img src="<?= base_url('assets/images/aadc_direct.png') ?>" alt="AADC Logo" style="height:61px; margin-right:10px;">
        <h4 class="card-title m-0">Al Ain Distribution Company (AADC) Utility Payment</h4>
    </div>

    <!-- Right: Action Buttons -->
    <div class="d-flex gap-2">
        <a href="<?= site_url('api/aadc/reports') ?>" class="btn btn-info">AADC Payment Reports</a>
        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
    </div>
</div>


        <div class="card">
            <div class="card-body">
                <form id="aadc-payment-form">
                    <div class="row mb-3">
                        <label>Select Search Type</label>
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="AccountID">Consumer Number</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="MobileNo">Mobile Number</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="PersonID">Person ID</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="EmiratesID">Emirates ID</button>
                        </div>
                    </div>

                    <div class="row mb-3" id="inputFields" style="display:none;">
                        <div class="col-md-4">
                            <label id="dynamicFieldLabel"></label>
                            <input type="text" id="searchValue" class="form-control">
                            <small id="accountError" class="text-danger" style="display:none;">Does not meet the required format.</small>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let selectedSearchType = '';

function validateInput(value, type) {
    const errorEl = $('#accountError');
    if (!value) {
        errorEl.text('Field is required.').show();
        return false;
    }

    switch (type) {
        case 'AccountID':
            if (!value.startsWith('6') || !/^[0-9]{10}$/.test(value)) {
                errorEl.text('Account number must start with 6 and be exactly 10 digits.').show();
                return false;
            }
            break;
        case 'MobileNo':
            if (!/^05\d{1}-\d{6,7}$/.test(value)) {
                errorEl.text('Mobile No format should be 05X-XXXXXXX').show();
                return false;
            }
            break;
        case 'PersonID':
            if (!/^\d{9,10}$/.test(value)) {
                errorEl.text('Person ID should be 9 - 10 digits long.').show();
                return false;
            }
            break;
        case 'EmiratesID':
            if (!/^\d{15}$/.test(value)) {
                errorEl.text('Emirates ID should be exactly 15 digits.').show();
                return false;
            }
            break;
        default:
            errorEl.text('Invalid search type.').show();
            return false;
    }

    errorEl.hide();
    return true;
}

function togglePayButton() {
    const value = $('#searchValue').val().trim();
    const valid = selectedSearchType && validateInput(value, selectedSearchType);
    $('#payBtn').prop('disabled', !valid);
}

$(document).ready(function () {
    $('.search-type-btn').on('click', function () {
        $('.search-type-btn').removeClass('active');
        $(this).addClass('active');
        selectedSearchType = $(this).data('type');
        $('#dynamicFieldLabel').text($(this).text());
        $('#searchValue').attr('name', selectedSearchType);
        $('#inputFields').show();
        $('#searchValue').val('');
        $('#balanceResultSection').hide();
        $('#accountError').hide();
        $('#payBtn').prop('disabled', true);
    });

    $('#searchValue').on('input', function () {
        togglePayButton();
    });

    // Check balance
    $('#payBtn').click(async function () {
        const value = $('#searchValue').val().trim();

        try {
            const balanceRes = await fetch("<?= site_url('api/aadc/fetch-bill') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    account_number: value,
                    req_type: selectedSearchType
                })
            });

            const balance = await balanceRes.json();
            if (!balance.status) {
                Swal.fire('Error', balance.message || 'Balance fetch failed.', 'error');
                return;
            }

            let html = '';
            const data = balance.data;

            // Show top-level fields except arrayResponse
            for (const key in data) {
                if (key !== 'arrayResponse') {
                    html += `<tr><th>${key}</th><td>${data[key]}</td></tr>`;
                }
            }

            // Show arrayResponse[0] fields
            if (data.arrayResponse && Array.isArray(data.arrayResponse) && data.arrayResponse.length > 0) {
                const detail = data.arrayResponse[0];
                const commission = 2;
                const balanceAmount = parseFloat(detail.Balance || 0);
                const finalAmount = (balanceAmount + commission).toFixed(2);

                for (const key in detail) {
                    if (key === 'Balance') {
                        html += `<tr>
                            <th>${key}</th>
                            <td><input type="number" min="0" step="0.01" id="editableBalance" class="form-control" value="${balanceAmount.toFixed(2)}" /></td>
                        </tr>`;
                    } else {
                        html += `<tr><th>${key}</th><td>${detail[key]}</td></tr>`;
                    }
                }

                html += `<tr><th>Commission</th><td>${commission.toFixed(2)} AED</td></tr>`;
                html += `<tr><th>Final Payable Amount (AED)</th><td><input type="text" id="finalAmount" class="form-control" value="${finalAmount} AED" readonly /></td></tr>`;

                $('#continueBtn').text(`Pay Amount: ${finalAmount} AED`);
                $('#continueBtn').data('payload', {
                
         account_number: value,
    amount_without_commission: balanceAmount.toFixed(2),
    amount_with_commission: finalAmount,
    oid: balance.oid,
    secure_sign: balance.secure_sign,
    timestamp: balance.timestamp
                });
            }

            $('#balanceDetails').html(html);
            $('#balanceResultSection').show();

           $('#editableBalance').on('input', function () {
    const newBalance = parseFloat($(this).val()) || 0;
    const commission = 2;
    const finalPayable = (newBalance + commission).toFixed(2);

    $('#finalAmount').val(`${finalPayable} AED`);
    $('#continueBtn').text(`Pay Amount: ${finalPayable} AED`);

    const currentPayload = $('#continueBtn').data('payload') || {};
    currentPayload.amount_without_commission = newBalance.toFixed(2);
    currentPayload.amount_with_commission = finalPayable; // ✅ correct key
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

            
            const payRes = await fetch("<?= site_url('api/aadc/make-payment') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const payResult = await payRes.json();
               if (payResult.status) {
                Swal.fire('Success', 'Transaction successful!', 'success');
                setTimeout(() => { window.location.href = "aadc/reports"; }, 2000);
            } else {
                Swal.fire('Failed',  payResult.message  || 'Transaction failed.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });
});
</script>
<?= $this->endSection() ?>
