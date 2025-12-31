<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid cart-container">
    <h2>Cart</h2>
</div>
<div class="container">

    <div class="py-5">
        <div class="card py-3 mb-4" style="border: 1px solid #e1e1e1; background-color: transparent;">
            <div class="card-body">
                <h2 class="cart-header">Your cart is currently empty.</h2>
            </div>
        </div>
        <p>Why not return to our amazing shop and start filling it with products. Just click on the button below to instantly get back to the shop page. Oh, and while you’re there, check out all of our mind-blowing discounts.</p>
        <div class="d-flex justify-content-center align-content-center py-4 my-4 return-btn">
            <a href="<?= site_url('/'); ?>" class="cart-button">return to shop</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>