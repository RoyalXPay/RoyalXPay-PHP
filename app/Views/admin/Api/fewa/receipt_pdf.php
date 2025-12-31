<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Receipt</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 14px; margin: 0; padding: 0; }
        .container { width: 700px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo img { max-height: 60px; }
        .invoice-info { text-align: right; font-size: 13px; }
        .invoice-title { text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .section { margin-bottom: 15px; }
        .section h4 { margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 5px; font-size: 16px; }
        .details p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table td, table th { padding: 8px; border: 1px solid #ccc; }
        .items th, .transactions th { background: #f4f4f4; }
        .total { text-align: right; font-weight: bold; font-size: 16px; padding: 10px; border-top: 2px solid #000; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-title">INVOICE</div>

        <div class="header">
            <div class="logo">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Company Logo" width="50%">
            </div>
            <div class="invoice-info">
                <div><strong>Invoice No:</strong> <?= esc($txn['transaction_id']) ?></div>
                <div><strong>Date:</strong> <?= esc(date('d-m-Y H:i:s', strtotime($txn['created_at']))) ?></div>
            </div>
        </div>

        <div class="section">
            <h4>Merchant Details</h4>
            <div class="details">
                <p><strong>Merchant Name:</strong> <?= esc($merchant['name'] ?? 'N/A') ?></p>
<p><strong>Merchant Email:</strong> <?= esc($merchant['email'] ?? 'N/A') ?></p>
<p><strong>Merchant Mobile:</strong> <?= esc($merchant['phone'] ?? 'N/A') ?></p>
            </div>
        </div>

        <div class="section">
            <h4>Customer Details</h4>
            <div class="details">
                <p><strong>Customer Name:</strong> <?= esc($customer['name'] ?? $txn['customer_name']) ?></p>
                <p><strong>Customer Email:</strong> <?= esc($customer['email'] ?? 'N/A') ?></p>
                <p><strong>Customer Mobile:</strong> <?= esc($customer['phone'] ?? $txn['customer_mobile']) ?></p>
            </div>
        </div>

        <div class="section">
            <h4>Item Details</h4>
            <table class="items">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Amount (AED)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Fewa Payment Bill</td>
                        <td>1</td>
                        <td><?= number_format($txn['amount'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h4>Transaction Details</h4>
            <table class="transactions">
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Mode</th>
                        <th>Amount (AED)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= esc(date('d-m-Y H:i:s', strtotime($txn['created_at']))) ?></td>
                        <td><?= esc($txn['mode'] ?? 'Online') ?></td>
                        <td><?= number_format($txn['amount'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total">
            Total Amount: AED <?= number_format($txn['amount'], 2) ?>
        </div>

        <div class="footer">
            This is a system-generated receipt. No signature required.<br>
            © <?= date('Y') ?> RoyalXPay. All rights reserved.
        </div>
    </div>
</body>
</html>
