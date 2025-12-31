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
        <h4 class="card-title mb-3">Online Charity Payment</h4>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-end gap-2">
                <a href="<?= site_url('api/onlinecharaty/reports') ?>" class="btn btn-info">Online Charity Payment Reports</a>
                <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-dark">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="onlinecharaty-payment-form">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Mobile Number</label>
                            <input type="text" id="account_number" class="form-control" placeholder="Enter Mobile Number">
                            <small id="accountError" class="text-danger" style="display:none;">Invalid Mobile Number</small>
                        </div>

                        <div class="col-md-4">
                            <label>Charity Type (reqField1)</label>
                            <select id="reqField1" class="form-control">
                                <option value="ZakatFund" selected>ZakatFund</option>
                                <option value="Sadaqa">Sadaqa</option>
                                <option value="Waqf">Waqf</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Product Details (reqField2)</label>
                            <select id="reqField2" class="form-control">
                                <option value="Zakat" selected>Zakat</option>
                                <option value="Sadaqa">Sadaqa</option>
                                <option value="Waqf">Waqf</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Amount (AED)</label>
                            <input type="number" min="1" id="paidAmount" class="form-control" placeholder="Enter Amount">
                        </div>
                        <div class="col-md-4">
                            <label>Commission (AED)</label>
                            <input type="number" min="1" id="paidAmount" readonly value="2" class="form-control" placeholder="Enter Amount">
                        </div>
                    </div>
                    

                    <input type="hidden" id="transaction_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <button type="button" id="payBtn" class="btn btn-success w-100">Pay</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function generateTransactionId() {
        const now = new Date();
        const uaeOffset = 4 * 60;
        const localOffset = now.getTimezoneOffset();
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

    $('#payBtn').click(async function () {
        const accountNumber = $('#account_number').val().trim();
        const reqField1 = $('#reqField1').val();
        const reqField2 = $('#reqField2').val();
        const amount = parseFloat($('#paidAmount').val().trim());

        if (!/^[0-9]{10}$/.test(accountNumber)) {
            $('#accountError').show();
            return;
        }
        $('#accountError').hide();

        if (!amount || amount <= 0) {
            Swal.fire('Error', 'Please enter a valid amount', 'error');
            return;
        }

        const transactionId = generateTransactionId();
        $('#transaction_id').val(transactionId);

        try {
            const tokenRes = await fetch("<?= site_url('api/onlinecharaty/get-token') ?>", { method: 'POST' });
            const tokenData = await tokenRes.json();

            if (!tokenData.token) {
                Swal.fire('Error', 'Failed to get token', 'error');
                return;
            }

           
            
            const payRes = await fetch("<?= site_url('api/onlinecharaty/make-payment') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                        token: tokenData.token,
                        transactionId: transactionId,
                        merchantId: "3684",
                        merchantLocation: "KL MBME",
                        method: "pay",
                        serviceId: "999",
                        paymentMode: "Cash",
                        paidAmount: amount,
                        lang: "en",
                        reqField1: reqField1,
                        reqField2: reqField2
                    })
            });

            const payResult = await payRes.json();
            console.log("Payment Result:", payResult);

            if (payResult.responseCode === '000' || payResult.status === 'SUCCESS') {
                Swal.fire('Success', 'Payment successful!', 'success');
                setTimeout(() => {
                    window.location.href = "<?= site_url('api/onlinecharaty/reports') ?>";
                }, 2000);
            } else {
                Swal.fire('Failed', payResult.billerMessage || payResult.message || 'Payment failed.', 'error');
            }

        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });
</script>
<?= $this->endSection() ?>
