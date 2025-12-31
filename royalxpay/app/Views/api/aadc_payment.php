<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="container py-5">
    <h2>AADC Bill Payment</h2>

    <form id="aadcForm">
        <div>
            <label for="account_number">Account Number:</label>
            <input type="text" id="account_number" name="account_number" required>
        </div>

        <div>
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>

        <div>
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>
        </div>

        <div>
            <label for="generated_id">Transaction ID:</label>
            <input type="text" id="generated_id" name="generated_id" readonly>
        </div>

        <div class="mt-3">
            <button type="submit">Pay Now</button>
            <button type="button" id="checkBalanceBtn">Check Bill (Balance)</button>
        </div>
    </form>

    <pre id="result" style="background:#f4f4f4;padding:1rem;margin-top:1rem;"></pre>
</section>

<script>
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
    const merchantId = "3684";
    return `${YY}${MM}${DD}${HH}${mm}${SS}${MS}${randomDigit}${merchantId}`;
}

document.getElementById('aadcForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const account = document.getElementById('account_number').value.trim();
    const name = document.getElementById('customer_name').value.trim();
    const amount = document.getElementById('amount').value.trim();
    const resultBox = document.getElementById('result');

    const transactionId = await generateTransactionId();
    document.getElementById('generated_id').value = transactionId;

    resultBox.innerText = 'Processing payment...';

    try {
        const tokenRes = await fetch('/api/aadc-payment/get-token', { method: 'POST' });
        const tokenData = await tokenRes.json();

        if (!tokenData.token) {
            resultBox.innerText = 'Token Error: ' + (tokenData.error || 'Unknown error');
            return;
        }

        const payRes = await fetch('/api/aadc-payment/make-payment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                token: tokenData.token,
                account_number: account,
                amount: amount,
                customer_name: name,
                transaction_id: transactionId
            })
        });

        const payResult = await payRes.json();
        resultBox.innerText = '💳 Payment Response:\n' + JSON.stringify(payResult, null, 2);
    } catch (error) {
        resultBox.innerText = '❌ Request failed: ' + error.message;
    }
});

document.getElementById('checkBalanceBtn').addEventListener('click', async function () {
    const account = document.getElementById('account_number').value.trim();
    const resultBox = document.getElementById('result');
    const transactionId = await generateTransactionId();
    document.getElementById('generated_id').value = transactionId;

    resultBox.innerText = 'Checking balance...';

    try {
        const tokenRes = await fetch('/api/aadc-payment/get-token', { method: 'POST' });
        const tokenData = await tokenRes.json();

        if (!tokenData.token) {
            resultBox.innerText = 'Token Error: ' + (tokenData.error || 'Unknown error');
            return;
        }

        const balanceRes = await fetch('/api/aadc-payment/fetch-bill', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                token: tokenData.token,
                account_number: account,
                transaction_id: transactionId
            })
        });

        const balanceResult = await balanceRes.json();
        resultBox.innerText = '📄 Balance Response:\n' + JSON.stringify(balanceResult, null, 2);
    } catch (error) {
        resultBox.innerText = '❌ Balance request failed: ' + error.message;
    }
});
</script>

<?= $this->endSection() ?>
