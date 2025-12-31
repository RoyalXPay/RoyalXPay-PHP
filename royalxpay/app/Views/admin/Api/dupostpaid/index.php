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
        <h4 class="card-title mb-3">Du postpaid Payment</h4>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-end gap-2">
                <a href="<?= site_url('api/dupostpaid/reports') ?>" class="btn btn-info">Du postpaid Payment Reports</a>
                <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="dupostpaid-payment-form">
                    <div class="row mb-3">
                        <label>Select Search Type</label>
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="AccountID">Mobile Number</button>
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
                if (!/^[0-9]{10}$/.test(value)) {
                    errorEl.text('Account number should be 6 - 10  digits.').show();
                    return false;
                }
                break;
            case 'MobileNo':
                if (!/^05\d{1}-\d{6,7}$/.test(value)) {
                    errorEl.text('Mobile No format should be 05X-XXXXXXX (7-11 digits).').show();
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

        $('#payBtn').click(async function () {
            const value = $('#searchValue').val().trim();
            const transactionId = await generateTransactionId();
            $('#transaction_id').val(transactionId);

            try {
                const tokenRes = await fetch("<?= site_url('api/dupostpaid/get-token') ?>", { method: 'POST' });
                const tokenData = await tokenRes.json();
                if (!tokenData.token) {
                    Swal.fire('Error', 'Failed to get token', 'error');
                    return;
                }

                const balanceRes = await fetch("<?= site_url('api/dupostpaid/fetch-bill') ?>", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        token: tokenData.token,
                        account_number: value,
                        transaction_id: transactionId,
                        // req_type: selectedSearchType
                    })
                });

                const balance = await balanceRes.json();
                if (balance.responseCode !== '000') {
                    const billerMessage = balance.billerMessage || balance.responseMessage || 'Balance fetch failed.';
                    $('#accountError').text(billerMessage).show();
                    Swal.fire('Error', billerMessage, 'error');
                    return;
                }

               let html = '';
const data = balance.responseData;
console.log('Response Data:', data);

let balanceAmount = 0;
let commission = 2;
let finalAmount = 0;

// Show top-level fields
for (const key in data) {
    if (data.hasOwnProperty(key) && typeof data[key] !== 'object') {
        const label = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
        html += `<tr><th>${label}</th><td>${data[key]}</td></tr>`;
    }
}

// Get balance from arrayResponse if exists
if (Array.isArray(data.arrayResponse) && data.arrayResponse.length > 0) {
    const detail = data.arrayResponse[0];
    balanceAmount = parseFloat(detail.Balance || 0);

    if (detail.AccountType) html += `<tr><th>Account Type</th><td>${detail.AccountType}</td></tr>`;
    if (detail.Area) html += `<tr><th>Area</th><td>${detail.Area}</td></tr>`;
    if (detail.PremiseType) html += `<tr><th>Premise Type</th><td>${detail.PremiseType}</td></tr>`;
}

// If balance is not from arrayResponse, fallback to top-level
if (!balanceAmount && data.amountPaid) {
    balanceAmount = parseFloat(data.amountPaid);
}

// Calculate total
finalAmount = balanceAmount + commission;

// Always show editable fields
html += `
<tr>
    <th>Balance Amount</th>
    <td><input type="number" min="0" step="0.01" id="editableBalance" class="form-control" value="${balanceAmount}" /></td>
</tr>
<tr>
    <th>Commission (AED)</th>
    <td><input type="number" min="0" step="0.01" id="editableCommission" class="form-control" value="${commission.toFixed(2)}" /></td>
</tr>
<tr>
    <th>Final Payable Amount (AED)</th>
    <td><input type="text" id="finalAmount" class="form-control" value="${finalAmount.toFixed(2)} AED" readonly /></td>
</tr>`;

// Update continue button & show
$('#continueBtn').text(`Pay Amount: ${finalAmount.toFixed(2)} AED`);
$('#continueBtn').data('payload', {
    token: tokenData.token,
    account_number: value,
    customer_name: $('#customer_name').val(),
    transaction_id: transactionId,
    amount: finalAmount,
    commission: commission,
    balance: balanceAmount
});

$('#balanceDetails').html(html);
$('#balanceResultSection').show();

// Update on input change
$('#balanceDetails').on('input', '#editableBalance, #editableCommission', function () {
    const newBalance = parseFloat($('#editableBalance').val()) || 0;
    const newCommission = parseFloat($('#editableCommission').val()) || 2;
    const finalPayable = newBalance + newCommission;

    $('#finalAmount').val(`${finalPayable.toFixed(2)} AED`);
    $('#continueBtn').text(`Pay Amount: ${finalPayable.toFixed(2)} AED`);

    const currentPayload = $('#continueBtn').data('payload') || {};
    currentPayload.amount = finalPayable;
    currentPayload.commission = newCommission;
    currentPayload.paidAmount = newBalance.toFixed(2); // Only the balance

    currentPayload.balance = newBalance;
    $('#continueBtn').data('payload', currentPayload);
});



            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        });

        $('#continueBtn').click(async function () {
    const payload = $(this).data('payload');

    if (!payload || !payload.token || !payload.transaction_id || !payload.account_number || !payload.amount) {
        Swal.fire('Error', 'Missing payment details. Please re-check balance.', 'error');
        return;
    }

    // Ask for mobile number using SweetAlert
    const { value: mobile } = await Swal.fire({
        title: 'Enter Customer Mobile No.',
        input: 'tel',
        inputLabel: 'Mobile Number',
        inputPlaceholder: '05XXXXXXXX',
        inputAttributes: {
            maxlength: 15,
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        confirmButtonText: 'Continue to Pay',
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Mobile number is required';
            } else if (!/^\d{10,15}$/.test(value)) {
                return 'Enter a valid mobile number (10-15 digits)';
            }
            return null;
        }
    });

    if (!mobile) {
        return; // User cancelled
    }

    payload.customer_mobile = mobile;

// Get the current editable balance from the input field
const rawBalance = parseFloat($('#editableBalance').val()) || 0;
payload.paidAmount = rawBalance.toFixed(2);
 console.log('Response Data:', payload);

    try {
    const payRes = await fetch("<?= site_url('api/dupostpaid/make-payment') ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const payResult = await payRes.json();

    console.log('⚠️ Full payResult:', payResult); // Log full response
   
    if (
    (payResult.status === 'success' && payResult.data?.responseCode === '000') ||
    (payResult.responseCode === '000' && payResult.status === 'SUCCESS')
    ) {
        Swal.fire('Success', 'Transaction successful!', 'success');
        setTimeout(() => {
            window.location.href = "<?= site_url('api/dupostpaid/reports') ?>";
        }, 2000);
    } else if (
        (payResult.status === 'PENDING' || payResult.responseCode === '111')
    ) {
        Swal.fire('Pending', 'Transaction is still pending. Please check back later or contact support.', 'info');
    setTimeout(() => {
                    window.location.href = "<?= site_url('api/dupostpaid/reports') ?>";
                }, 2000);
    } else {
        Swal.fire('Failed', payResult?.data?.responseMessage || payResult?.message || 'Transaction failed. Please try again.', 'error');
    setTimeout(() => {
                    window.location.href = "<?= site_url('api/dupostpaid/reports') ?>";
                }, 2000);
    }

} catch (err) {
    console.error('❌ JS error:', err);
    Swal.fire('Error', err.message, 'error');
}

});


        // $('#continueBtn').click(async function () {
        //     const payload = $(this).data('payload');
        //     if (!payload || !payload.token || !payload.transaction_id || !payload.account_number || !payload.amount) {
        //         Swal.fire('Error', 'Missing payment details. Please re-check balance.', 'error');
        //         return;
        //     }

        //     try {
        //         const payRes = await fetch("<?= site_url('api/dupostpaid/make-payment') ?>", {
        //             method: 'POST',
        //             headers: { 'Content-Type': 'application/json' },
        //             body: JSON.stringify(payload)
        //         });

        //         const payResult = await payRes.json();

        //         if (payResult.responseCode === '000') {
        //             Swal.fire('Success', 'Transaction successful!', 'success');
                    
        //             setTimeout(() => {
        //                 window.location.href = "dupostpaid/reports";
        //             }, 2000);
        //         } else {
        //             Swal.fire('Failed', payResult.billerMessage || 'Transaction failed. Your Wallet Balance is Less to proceed, Please recharge it.', 'error');
        //         }
        //     } catch (err) {
        //         Swal.fire('Error', err.message, 'error');
        //     }
        // });
    });
</script>
<?= $this->endSection() ?>
