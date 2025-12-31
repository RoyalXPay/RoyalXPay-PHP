<form action="/api/make-payment" method="POST">
  <input type="text" name="customerMobile" placeholder="Mobile Number" required>
  <select name="serviceId">
    <option value="DU_PREPAID">Du Prepaid</option>
    <option value="ETISALAT">Etisalat</option>
  </select>
  <input type="number" name="amount" placeholder="Amount" required>
  <input type="hidden" name="token" id="accessToken">
  <button type="submit">Pay</button>
</form>

<script>
  fetch('/api/get-token', { method: 'POST' })
    .then(res => res.json())
    .then(data => {
      document.getElementById('accessToken').value = data.access_token;
    });
</script>
