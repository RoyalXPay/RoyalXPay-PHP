<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid cart-container my-5">
    <h2 class="text-center mb-4">Checkout</h2>
</div>

<div class="container">
    <form action="<?= site_url('checkout/placeOrder') ?>" method="post" id="checkoutForm" class="needs-validation" novalidate>
        <?= csrf_field() ?>
        <?= view('flash_messages'); ?>

        <div class="billing-overview pb-5">
            <div class="row gy-5">
                <!-- Billing Details -->
                <div class="col-lg-6">
                    <h4 class="mb-4 fw-bold">Billing Details</h4>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="name" name="name" readonly placeholder="Name" value="<?= $userDetails["name"]; ?>">
                        <label for="name">Name</label>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="Company" name="company" placeholder="Company Name">
                        <label for="Company">Company Name</label>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="Country" name="country" placeholder="Country" required>
                        <label for="Country">Country *</label>
                        <div class="invalid-feedback">Country is required</div>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="Address" name="address" placeholder="Street Address" required>
                        <label for="Address">Street Address *</label>
                        <div class="invalid-feedback">Please enter your street address</div>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="City" name="city" placeholder="City" required>
                        <label for="City">City *</label>
                        <div class="invalid-feedback">Please enter a valid city</div>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="State" name="state" placeholder="State" required>
                        <label for="State">State *</label>
                        <div class="invalid-feedback">Please enter your state</div>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="Pincode" name="pincode" placeholder="Pincode" required pattern="[0-9]{5,10}">
                        <label for="Pincode">Postal Code *</label>
                        <div class="invalid-feedback">Enter a valid postal code</div>
                    </div>

                </div>

                <!-- Order Summary -->
                <div class="col-lg-6">
                    <h4 class="mb-4 fw-bold">Your Order</h4>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <table class="table mb-0 table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td><?= esc($item['product_name']) ?> × <?= $item['quantity'] ?></td>
                                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Subtotal</th>
                                        <td>$<?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Shipping</th>
                                        <td>$<?= number_format($shippingCost, 2) ?></td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <th>Total</th>
                                        <td>$<?= number_format($total, 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="fw-semibold">Order Notes</label>
                        <textarea class="form-control" id="comment" name="order_notes" rows="4" placeholder="Notes about your order (optional)"></textarea>
                    </div>

                    <div class="card bg-light p-3">
                        <p class="mb-0 text-muted small">No payment methods available. Please contact us for alternatives.</p>
                    </div>

                    <p class="mt-4 small text-muted">
                        Your personal data will be used to process your order and support your experience as described in our
                        <a href="#" class="text-decoration-underline">Privacy Policy</a>.
                    </p>

                    <div class="text-center mt-4">
                        <button type="submit" class="cart-button" id="submitButton">
                            Place Order
                            <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const submitButton = document.getElementById('submitButton');
        const spinner = document.getElementById('spinner');

        submitButton.disabled = true;
        spinner.classList.remove('d-none');
        submitButton.innerHTML = 'Processing...';
    });
</script>
<?= $this->endSection() ?>