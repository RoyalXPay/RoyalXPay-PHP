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
        <img src="<?= base_url('assets/images/national_bond.png') ?>" alt="National Bond Logo" style="height:61px; margin-right:10px;">
        <h4 class="card-title m-0">National Bond</h4>
    </div>

    <!-- Right: Action Buttons -->
    <div class="d-flex gap-2">
        <a href="<?= site_url('api/nationalbond/reports') ?>" class="btn btn-info">National Bond Topup Payment Reports</a>
        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
    </div>
</div>


        <div class="card">
            <div class="card-body">
                <form id="nationalbond-payment-form">
                    <div class="row mb-3">
                        <label>Select Search Type</label>
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="AccountID">Account number</button>
                            <!-- <button type="button" class="btn btn-outline-primary search-type-btn" data-type="MobileNo">Mobile Number</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="PersonID">Person ID</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="EmiratesID">Emirates ID</button>
                         -->
                        </div>
                    </div>

                    <div class="row mb-3" id="inputFields" style="display:none;">
                        <div class="col-md-4">
                            <label id="dynamicFieldLabel"></label>
                            <input type="text" id="searchValue" name="" class="form-control">
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

        switch (type) {
            case 'AccountID':
            if (!/^\d{4,10}$/.test(value)) {
                errorEl.text('Account ID should be 4 - 10 digits long.').show();
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

    // async function generateTransactionId() {
    //     const now = new Date();
    //     const YY = String(now.getFullYear()).slice(2);
    //     const MM = String(now.getMonth() + 1).padStart(2, '0');
    //     const DD = String(now.getDate()).padStart(2, '0');
    //     const HH = String(now.getHours()).padStart(2, '0');
    //     const mm = String(now.getMinutes()).padStart(2, '0');
    //     const SS = String(now.getSeconds()).padStart(2, '0');
    //     const MS = String(Math.floor(now.getMilliseconds() / 10)).padStart(2, '0');
    //     const randomDigit = Math.floor(Math.random() * 10);
    //     return `${YY}${MM}${DD}${HH}${mm}${SS}${MS}${randomDigit}3684`;
    // }

    function generateTransactionId() {
        const now = new Date();

        // Convert to UAE time (UTC+4)
        const uaeOffset = 4 * 60; // 4 hours in minutes
        const localOffset = now.getTimezoneOffset(); // in minutes
        const uaeTime = new Date(now.getTime() + (uaeOffset + localOffset) * 60 * 1000);

        const YY = String(uaeTime.getFullYear()).slice(2);
        const MM = String(uaeTime.getMonth() + 1).padStart(2, '0');
        const DD = String(uaeTime.getDate()).padStart(2, '0');
        const HH = String(uaeTime.getHours()).padStart(2, '0');
        const mm = String(uaeTime.getMinutes()).padStart(2, '0');
        const SS = String(uaeTime.getSeconds()).padStart(2, '0');
        const MS = String(Math.floor(uaeTime.getMilliseconds() / 10)).padStart(2, '0');
        const randomDigit = Math.floor(Math.random() * 10);
        
        return `${YY}${MM}${DD}${HH}${mm}${SS}${MS}${randomDigit}3684`;
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
            const balanceRes = await fetch("<?= site_url('api/nationalbond/fetch-bill') ?>", {
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
console.log("Balance Response:", balance);

            // Inside #payBtn click
// Always pick from balance.data
let data = balance.data;

// Build table HTML
let html = '';
for (const key in data) {
    if (key !== 'resField2') {
        html += `<tr><th>${key}</th><td>${data[key]}</td></tr>`;
    }
}

// Extract amount from resField2
const commission = 2;
let balanceAmount = Number(data.resField2);   // 👈 Force number
const finalAmount = (balanceAmount + commission).toFixed(2);

// Append amount rows
html += `<tr>
    <th>Amount</th>
    <td><input type="number" min="50" step="50" id="editableBalance" 
               class="form-control" value="${balanceAmount}" /></td>
</tr>`;
html += `<tr><th>Commission</th><td>${commission} AED</td></tr>`;
html += `<tr><th>Final Payable Amount (AED)</th>
    <td><input type="text" id="finalAmount" class="form-control" 
               value="${finalAmount} AED" readonly /></td>
</tr>`;

// Show section
$('#balanceDetails').html(html);
$('#balanceResultSection').show();

// Prepare payload
$('#continueBtn').text(`Pay Amount: ${finalAmount} AED`).data('payload', {
    account_number: data.accountNumber || $('#searchValue').val(),
    amount_without_commission: balanceAmount,
    amount_with_commission: finalAmount,
    provider_transaction_id: data.providerTransactionId || '',
    cust_name: data.custName || 'Web Portal User',
    oid: balance.oid,
    secure_sign: balance.secure_sign,
    timestamp: balance.timestamp
});

// Recalculate if user edits amount
$('#editableBalance').on('input', function () {
    let newBalance = parseFloat($(this).val()) || 0;
    if (newBalance % 50 !== 0) {
        $('#finalAmount').val('Must be multiple of 50 AED');
        $('#continueBtn').prop('disabled', true);
        return;
    }
    $('#continueBtn').prop('disabled', false);

    const newFinal = (newBalance + commission).toFixed(2);
    $('#finalAmount').val(`${newFinal} AED`);
    $('#continueBtn').text(`Pay Amount: ${newFinal} AED`);

    let payload = $('#continueBtn').data('payload') || {};
    payload.amount_without_commission = newBalance;
    payload.amount_with_commission = newFinal;
    $('#continueBtn').data('payload', payload);
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

            
            const payRes = await fetch("<?= site_url('api/nationalbond/make-payment') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const payResult = await payRes.json();
            if (payResult.status) {
                Swal.fire('Success', 'Transaction successful!', 'success');
                setTimeout(() => { window.location.href = "nationalbond/reports"; }, 2000);
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
