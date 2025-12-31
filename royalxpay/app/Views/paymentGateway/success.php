<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin-top: 100px;
        }

        .card {
            border: none;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }

        .success-icon {
            font-size: 100px;
            color: #28a745;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-top: 20px;
        }

        .lead {
            font-size: 1.2rem;
            color: #555;
            margin-top: 20px;
        }

        .footer-text {
            font-size: 1rem;
            color: #7f8c8d;
            margin-top: 30px;
        }

        .view-order-btn {
            margin-top: 30px;
            font-size: 1.1rem;
            color: #fff;
            background-color: #28a745;
            border-radius: 5px;
            padding: 12px 30px;
            text-decoration: none;
        }

        .view-order-btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="mt-3">Payment Successful!</h1>
            <p class="lead"><?php echo session()->getFlashdata('message'); ?></p>
            <p class="footer-text">Thank you for your payment. Your order is being processed, and we will notify you once it's ready.</p>
            <!-- View Order Button -->
            <a href="#" class="view-order-btn">Thank You.</a>
        </div>
    </div>

</body>

</html>