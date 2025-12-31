<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AADC Payment Receipt</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h2 { text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table td, .table th { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>
    <h2>AADC Payment Receipt</h2>
    <table class="table">
        <tr>
            <th>Transaction ID</th>
            <td><?= esc($txn['transaction_id']) ?></td>
        </tr>
        <tr>
            <th>Account Number</th>
            <td><?= esc($txn['account_number']) ?></td>
        </tr>
        <tr>
            <th>Amount (AED)</th>
            <td><?= esc($txn['amount']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= esc($txn['status']) ?></td>
        </tr>
        <tr>
            <th>Date & Time</th>
            <td><?= esc(date('d-m-Y H:i:s', strtotime($txn['created_at']))) ?></td>
        </tr>
    </table>
</body>
</html>
