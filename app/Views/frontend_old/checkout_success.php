<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </div>
                    <h2 class="mb-3">Order Placed Successfully!</h2>
                    <p class="lead mb-4">Thank you for your order. Your order number is <strong>#<?= $order['order_id'] ?></strong></p>

                    <div class="order-summary bg-light p-4 rounded mb-4 text-start">
                        <h5 class="mb-3">Order Summary</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                                <p><strong>Total Amount:</strong> $<?= number_format($order['total'], 2) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Shipping To:</strong></p>
                                <address>
                                    <?= $order['address'] ?><br>
                                    <?= $order['city'] ?>, <?= $order['state'] ?><br>
                                    <?= $order['country'] ?> - <?= $order['pincode'] ?>
                                </address>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="<?= site_url('/') ?>" class="btn btn-outline-primary">Continue Shopping</a>
                        <a href="<?= site_url('account/orders') ?>" class="btn btn-primary">View My Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>