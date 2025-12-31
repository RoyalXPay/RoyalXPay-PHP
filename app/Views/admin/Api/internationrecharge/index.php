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
        <h4 class="card-title mb-3">Internation Recharge Utility Payment</h4>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-end gap-2">
                <a href="<?= site_url('api/internationrecharge/reports') ?>" class="btn btn-info">Internation Recharge Payment Reports</a>
                <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="internationrecharge-payment-form">
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
        <div class="input-group">
            <select id="countryCode" class="form-select" style="max-width: 100px; display: none;">
                <option value="+971">🇦🇪 +971</option>
                <option value="+91">🇮🇳 +91</option>
                <option value="+92">🇵🇰 +92</option>
                <option value="+880">🇧🇩 +880</option>
            </select>
            <input type="text" id="searchValue" name="" class="form-control">
        </div>
        <small id="accountError" class="text-danger" style="display:none;">Does not meet the required format.</small>
    </div>
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
                    errorEl.text('Mobile No format should be 05X-XXXXXXX (7-11 digits)').show();
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

            // Show country code dropdown only for mobile number
    
        $('#countryCode').show();
    
        });

        $('#searchValue').on('input', function () {
            togglePayButton();
        });

        $('#payBtn').click(async function () {
             const countryCode = $('#countryCode').val().replace('+', ''); // remove "+" sign
    const value = $('#searchValue').val().trim();
    const fullNumber = countryCode + value; // ✅ Combine country code with number
            const transactionId = await generateTransactionId();
            $('#transaction_id').val(transactionId);

            try {
                const tokenRes = await fetch("<?= site_url('api/internationrecharge/get-token') ?>", { method: 'POST' });
                const tokenData = await tokenRes.json();
                if (!tokenData.token) {
                    Swal.fire('Error', 'Failed to get token', 'error');
                    return;
                }
                console.log('Response Data:', fullNumber);
                console.log('Response selectedSearchType:', selectedSearchType);

                const balanceRes = await fetch("<?= site_url('api/internationrecharge/fetch-bill') ?>", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        token: tokenData.token,
                        account_number: fullNumber,
                        transaction_id: transactionId,
                        req_type: selectedSearchType
                    })
                });

                const balance = await balanceRes.json();
                                console.log('Response balance:', balance);

                if (balance.responseCode !== '000') {
                    const billerMessage = balance.billerMessage || balance.responseMessage || 'Balance fetch failed.';
                    $('#accountError').text(billerMessage).show();
                    Swal.fire('Error', billerMessage, 'error');
                    return;
                }

                let html = '';
                const data = balance.responseData;
                
                console.log('Response Data:', data);
                if (data.accountNumber) {
                        html += `<tr><th>Account Number</th><td>${data.accountNumber}</td></tr>`;
                    }
                if (data && data.resField3 && Array.isArray(data.resField3) && data.resField3.length > 0) {
    const skuList = data.resField3; // Available products
    const selectedSku = skuList[0]; // Default to first SKU (or let user select later)
    const skuCode = selectedSku.SkuCode;
    const skuValue = selectedSku.Maximum.SendValue;

    const commission = 2;
    const finalAmount = (parseFloat(skuValue) + commission).toFixed(2);

    let html = '';

    // Show SKU dropdown if multiple SKUs
    html += `<tr>
                <th>Product</th>
                <td>
                    <select id="skuSelect" class="form-select">`;
    skuList.forEach(sku => {
        html += `<option value="${sku.SkuCode}" data-value="${sku.Maximum.SendValue}">
                    ${sku.DefaultDisplayText} - AED ${sku.Maximum.SendValue}
                </option>`;
    });
    html += `</select>
            </td>
        </tr>`;

    html += `<tr><th>Commission</th><td>${commission.toFixed(2)} AED</td></tr>`;
    html += `<tr>
                <th>Final Payable Amount (AED)</th>
                <td><input type="text" id="finalAmount" class="form-control" value="${finalAmount} AED" readonly /></td>
            </tr>`;

    $('#continueBtn').text(`Pay Amount: ${finalAmount} AED`);
    $('#continueBtn').data('payload', {
        token: tokenData.token,
        account_number: $('#countryCode').val().replace('+', '') + value, // Mobile incl. country code
        customer_name: $('#customer_name').val(),
        transaction_id: transactionId,
        amount: finalAmount,
        reqField2: skuCode,
        reqField3: skuValue
    });

    $('#balanceDetails').html(html);
    $('#balanceResultSection').show();

    // Update price if user selects different SKU
    $('#skuSelect').on('change', function () {
        const selectedSkuValue = parseFloat($(this).find(':selected').data('value'));
        const commission = 2;
        const updatedFinal = (selectedSkuValue + commission).toFixed(2);

        $('#finalAmount').val(`${updatedFinal} AED`);
        $('#continueBtn').text(`Pay Amount: ${updatedFinal} AED`);

        const currentPayload = $('#continueBtn').data('payload');
        currentPayload.reqField2 = $(this).val();
        currentPayload.reqField3 = selectedSkuValue;
        currentPayload.amount = updatedFinal;

        $('#continueBtn').data('payload', currentPayload);
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
                    currentPayload.amount = finalPayable;
                    currentPayload.paidAmount = newBalance.toFixed(2); // Only the balance

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
        const payRes = await fetch("<?= site_url('api/internationrecharge/make-payment') ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const payResult = await payRes.json();

        if (payResult.responseCode === '000') {
            Swal.fire('Success', 'Transaction successful!', 'success');

            setTimeout(() => {
                window.location.href = "internationrecharge/reports";
            }, 2000);
        } else {
            Swal.fire('Failed', payResult.billerMessage || 'Transaction failed. Please try again.', 'error');
        }
    } catch (err) {
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
        //         const payRes = await fetch("<?= site_url('api/internationrecharge/make-payment') ?>", {
        //             method: 'POST',
        //             headers: { 'Content-Type': 'application/json' },
        //             body: JSON.stringify(payload)
        //         });

        //         const payResult = await payRes.json();

        //         if (payResult.responseCode === '000') {
        //             Swal.fire('Success', 'Transaction successful!', 'success');
                    
        //             setTimeout(() => {
        //                 window.location.href = "internationrecharge/reports";
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
