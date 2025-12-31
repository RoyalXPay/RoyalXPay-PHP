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
        <img src="<?= base_url('assets/images/nol.png') ?>" alt="Nol Logo" style="height:61px; margin-right:10px;">
        <h4 class="card-title m-0">Nol Topup</h4>
    </div>

    <!-- Right: Buttons -->
    <div class="d-flex gap-2">
        <a href="<?= site_url('api/noltopup/reports') ?>" class="btn btn-info">Nol Topup Payment Reports</a>
        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
    </div>
</div>

        <div class="card">
            <div class="card-body">
                <form id="noltopup-payment-form">
                    <div class="row mb-3">
                        <label>Select Search Type</label>
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="AccountID">Card Tag Number</button>
                        </div>
                    </div>

                    <div class="row mb-3" id="inputFields" style="display:none;">
                        <div class="col-md-4">
                            <label>Card Tag Number</label>
                            <input type="text" id="searchValue" name="AccountID" class="form-control">
                            <small id="accountError" class="text-danger" style="display:none;">Invalid input.</small>
                        </div>
                        <div class="col-md-4">
                            <label>Amount (AED)</label>
                            <input type="number" id="editableBalance" min="1" step="0.01" class="form-control" placeholder="Enter top-up amount" />
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

function validateInput(value, type) {
    const errorEl = $('#accountError');
    if (!value) {
        errorEl.text('Field is required.').show();
        return false;
    }

    if (type === 'AccountID' && !/^[0-9]{6,10}$/.test(value)) {
        errorEl.text('Card number must be 6 - 10 digits.').show();
        return false;
    }

    errorEl.hide();
    return true;
}

function togglePayButton() {
    const value = $('#searchValue').val().trim();
    const amount = $('#editableBalance').val().trim();
    const validAccount = selectedSearchType && validateInput(value, selectedSearchType);
    const validAmount = amount !== '' && parseFloat(amount) > 0;
    $('#payBtn').prop('disabled', !(validAccount && validAmount));
}



$(document).ready(function () {
    $('.search-type-btn').on('click', function () {
        $('.search-type-btn').removeClass('active');
        $(this).addClass('active');
        selectedSearchType = $(this).data('type');
        $('#searchValue').attr('name', selectedSearchType);
        $('#inputFields').show();
        $('#searchValue').val('');
        $('#editableBalance').val('');
        $('#accountError').hide();
        $('#balanceResultSection').hide();
        $('#payBtn').prop('disabled', true);
    });

    $('#searchValue, #editableBalance').on('input', function () {
        togglePayButton();
    });

  $('#payBtn').click(async function () {
    const value = $('#searchValue').val().trim();   // card tag number
    const amount = $('#editableBalance').val().trim(); // top-up amount

    try {
        const balanceRes = await fetch("<?= site_url('api/noltopup/fetch-bill') ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                account_number: value,
                topup_amount: amount,       // ✅ pass amount here
                req_type: selectedSearchType
            })
        });

        const balance = await balanceRes.json();
        if (!balance.status) {
            Swal.fire('Error', balance.message || 'Balance fetch failed.', 'error');
            return;
        }

 // build UI
let html = '';
const data = balance.data;

// always check fields exist
const accountNumber = data.accountNumber ?? '-';
const rechargeAmount = parseFloat($('#editableBalance').val().trim()) || parseFloat(data.amount) || 0;
const txnTime = data.resField1 ?? '-';
const referenceId = data.resField2 ?? '-';

// calculate commission + final payable
const commission = 2.00;
const finalPayable = (rechargeAmount + commission).toFixed(2);

html += `<tr><th>Card Tag Number</th><td>${accountNumber}</td></tr>`;
html += `<tr><th>Recharge Amount</th><td>${rechargeAmount.toFixed(2)} AED</td></tr>`;
html += `<tr><th>Transaction Time</th><td>${txnTime}</td></tr>`;
html += `<tr><th>Reference ID</th><td>${referenceId}</td></tr>`;
html += `<tr><th>Commission</th><td>${commission.toFixed(2)} AED</td></tr>`;
html += `<tr><th><b>Final Payable</b></th><td><b>${finalPayable} AED</b></td></tr>`;

// save payload for payment
$('#continueBtn').data('payload', {
    account_number: accountNumber,
    amount_without_commission: rechargeAmount.toFixed(2),  // ✅ without commission
    amount_with_commission: finalPayable,                  // ✅ with commission
    oid: balance.oid,
    secure_sign: balance.secure_sign,
    timestamp: balance.timestamp
});

$('#balanceDetails').html(html);
$('#balanceResultSection').show();


    } catch (err) {
        Swal.fire('Error', err.message, 'error');
    }
});

   $('#continueBtn').click(async function () {
    const payload = $(this).data('payload');
      if (!payload || !payload.account_number || !payload.amount_without_commission) {
        Swal.fire('Error', 'Missing payment details.', 'error');
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
        const payRes = await fetch("<?= site_url('api/noltopup/make-payment') ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const payResult = await payRes.json();
        if (payResult.status) {
            Swal.fire('Success', 'Transaction successful!', 'success');
            setTimeout(() => { window.location.href = "noltopup/reports"; }, 2000);
        } else {
            Swal.fire('Failed', payResult.message || 'Transaction failed.', 'error');
        }
    } catch (err) {
        Swal.fire('Error', err.message, 'error');
    }
});

});
</script>
<?= $this->endSection() ?>
