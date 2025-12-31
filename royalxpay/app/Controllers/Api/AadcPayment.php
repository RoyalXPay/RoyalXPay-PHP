<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-type-btn.active {
        background-color: #0d6efd !important; /* Dark blue */
        color: #fff !important;
        border-color: #0d6efd !important;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <h4 class="card-title mb-3">AADC Utility Payment</h4>
       <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end gap-2">
            <a href="<?= site_url('api/aadc/reports') ?>" class="btn btn-info">AADC Payment Reports</a>
            <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Search</button>
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
        </div>
    </div>
        
        <div class="card">
            <div class="card-body">
                <form id="aadc-payment-form">
                    <div class="row mb-3">
                        <label>Select Search Type</label>
                        <div class="col-md-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="consumer_number">Consumer Number</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="mobile_number">Mobile Number</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="person_id">Person ID</button>
                            <button type="button" class="btn btn-outline-primary search-type-btn" data-type="emirates_id">Emirates ID</button>
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
                        <div class="col-md-4 d-flex ">
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

   function validateAccountNumber(value) {
    const errorEl = $('#accountError');

    if (!value.startsWith('6')) {
        errorEl.text('Account number must start with 6.').show();
        return false;
    } 
    if (value === '') {
        errorEl.text('Account number is required.').show();
        return false;
    }

    if (!/^\d+$/.test(value)) {
        errorEl.text('Account number must contain only digits.').show();
        return false;
    }

    if (value.length !== 10) {
        errorEl.text(`Account number must be exactly 10 digits. You entered ${value.length} digits.`).show();
        return false;
    }

   

    errorEl.hide();
    return true;
}


   function togglePayButton() {
    const account = $('#searchValue').val().trim();
    const valid = selectedSearchType && validateAccountNumber(account);
    $('#payBtn').prop('disabled', !valid);
}

    async function generateTransactionId() {
        const now = new Date();
        const YY = String(now.getFullYear()).slice(2);
        const MM = String(now.getMonth() + 1).padStart(2, '0');
        const DD = String(now.getDate()).padStart(2, '0');
        const HH = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const SS = String(now.getSeconds()).padStart(2, '0');
        const MS = String(Math.floor(now.getMilliseconds() / 10)).padStart(2, '0');
        const randomDigit = Math.floor(Math.random() * 10);
        return `${YY}${MM}${DD}${HH}${mm}${SS}${MS}${randomDigit}3684`;
    }

    $(document).ready(function () {
       $('.search-type-btn').on('click', function () {
    $('.search-type-btn').removeClass('active'); // Remove from all
    $(this).addClass('active'); // Add to clicked

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
            const accountNumber = $('#searchValue').val().trim();
            const transactionId = await generateTransactionId();
            $('#transaction_id').val(transactionId);

            try {
                const tokenRes = await fetch("<?= site_url('api/aadc/get-token') ?>", { method: 'POST' });
                const tokenData = await tokenRes.json();
                if (!tokenData.token) {
                    Swal.fire('Error', 'Failed to get token', 'error');
                    return;
                }

                const balanceRes = await fetch("<?= site_url('api/aadc/fetch-bill') ?>", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        token: tokenData.token,
                        account_number: accountNumber,
                        transaction_id: transactionId
                    })
                });

                const balance = await balanceRes.json();
                if (balance.responseCode !== '000') {
                   const billerMessage = balance.billerMessage || balance.responseMessage || 'Balance fetch failed.';
    
    if (balance.responseCode === '302') {
        $('#accountError').text(billerMessage).show();
         Swal.fire('Check Account No.', billerMessage, 'error');
    } else {
        Swal.fire('Failed', billerMessage, 'error');
    }

    return;
                }

                let html = '';
                const data = balance.responseData;
                if (data) {
                    if (data.accountNumber) {
                        html += `<tr><th>Account Number</th><td>${data.accountNumber}</td></tr>`;
                    }
                    if (Array.isArray(data.arrayResponse) && data.arrayResponse.length > 0) {
                        const detail = data.arrayResponse[0];
                        const balanceAmount = parseFloat(detail.Balance || 0);
                        const commission = 2.00;
                        const finalAmount = (balanceAmount + commission).toFixed(2);

                        // Required Fields First
                    
                        if (detail.Balance) {
                            html += `<tr><th>Balance Amount</th><td>${balanceAmount.toFixed(2)} AED</td></tr>`;
                        }
                        if (detail.AccountType) {
                            html += `<tr><th>Account Type</th><td>${detail.AccountType}</td></tr>`;
                        }
                        if (detail.Area) {
                            html += `<tr><th>Area</th><td>${detail.Area}</td></tr>`;
                        }
                        if (detail.PremiseType) {
                            html += `<tr><th>Premise Type</th><td>${detail.PremiseType}</td></tr>`;
                        }

                        // Then Extra Calculated Fields
                        html += `<tr><th>Commission</th><td>${commission.toFixed(2)} AED</td></tr>`;
                        html += `<tr><th>Final Payable Amount (AED)</th>
                                    <td><input type="text" class="form-control" value="${finalAmount} AED" readonly /></td>
                                </tr>`;

                        // Update Pay Button Text
                        $('#continueBtn').text(`Pay Amount: ${finalAmount} AED`);

                        // Store full payload including amount
                        $('#continueBtn').data('payload', {
                            token: tokenData.token,
                            account_number: accountNumber,
                            customer_name: $('#customer_name').val(),
                            transaction_id: transactionId,
                            amount: finalAmount
                        });
                    }

                }

                $('#balanceDetails').html(html);
                $('#balanceResultSection').show();

                // $('#continueBtn').data('payload', {
                //     token: tokenData.token,
                //     account_number: accountNumber,
                //     customer_name: $('#customer_name').val(),
                //     transaction_id: transactionId
                // });

            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        });

        $('#continueBtn').click(async function () {
            const payload = $(this).data('payload');
            console.log(payload);
            if (!payload || !payload.token || !payload.transaction_id || !payload.account_number || !payload.amount) {
                Swal.fire('Error', 'Missing payment details. Please re-check balance.', 'error');
                return;
            }

            try {
                const payRes = await fetch("<?= site_url('api/aadc/make-payment') ?>", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const payResult = await payRes.json();

                if (payResult.responseCode === '000') {
                    Swal.fire('Success', 'Transaction successful!', 'success');
                } else {
                    Swal.fire('Failed', payResult.billerMessage || 'Transaction failed.', 'error');
                }
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        });



        $('#addWalletBtn').click(function () {
            Swal.fire('Info', 'Wallet recharge module is not implemented here.', 'info');
        });
    });
</script>

<?= $this->endSection() ?>
