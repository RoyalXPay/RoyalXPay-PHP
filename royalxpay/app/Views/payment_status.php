<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <style>
        body { text-align: center; font-family: Arial, sans-serif; padding: 50px; }
        .message { font-size: 20px; padding: 20px; display: inline-block; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php if ($response['status'] === 'success'): ?>
        <div class="message success"><?= esc($response['message']) ?></div>
    <?php else: ?>
        <div class="message error"><?= esc($response['message']) ?></div>
    <?php endif; ?>
</body>
</html>
