<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Video Call - RoyalXPay</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      background: #0d1b2a;
      color: white;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .loading {
      font-size: 18px;
      color: #ffffffb0;
    }
  </style>
</head>
<body>


  <script>
    // Auto-redirect to the video call
    window.onload = function() {
      const callUrl = "https://sip.dialtophone.com/customer/call.php?hotel_id=3&table=ROYALXPAY";

      // ✅ Use replace() instead of open() to stay in same tab
      window.location.replace(callUrl);
    };
  </script>
</body>
</html>
