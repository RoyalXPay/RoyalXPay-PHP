<h2>AADC Bill Payment</h2>
<form id="paymentForm">
    <input type="text" id="account_number" placeholder="Account Number" required><br>
    <input type="number" id="amount" placeholder="Amount" required><br>
    <input type="text" id="customer_name" placeholder="Customer Name" required><br>
    <button type="submit">Pay Now</button>
</form>

<div id="paymentResult"></div>

<script>
document.getElementById('paymentForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const accountNumber = document.getElementById('account_number').value;
    const amount = document.getElementById('amount').value;
    const customerName = document.getElementById('customer_name').value;

    // Get token
    const tokenRes = await fetch('/api/get-token', {
        method: 'POST'
    });
    const tokenData = await tokenRes.json();

    if (!tokenData.token) {
        document.getElementById('paymentResult').innerText = 'Token generation failed.';
        return;
    }

    // Make payment
    const paymentRes = await fetch('/api/make-payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            token: tokenData.token,
            account_number: accountNumber,
            amount: amount,
            customer_name: customerName
        })
    });

    const result = await paymentRes.json();
    document.getElementById('paymentResult').innerText = JSON.stringify(result, null, 2);
});
</script>
