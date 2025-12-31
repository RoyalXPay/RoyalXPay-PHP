<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid cart-container">
    <h2>Cart</h2>
</div>
<div class="container">
    <div class="pt-5">
        <div class="row">
            <div id="modalMessages"></div>

            <?php if (empty($cartItems)): ?>

                <div class="card py-3" style="border: 1px solid #e1e1e1; background-color: transparent;">
                    <div class="card-body">
                        <h2 class="cart-header">Your cart is currently empty.</h2>
                    </div>
                </div>
                <p>Why not return to our amazing shop and start filling it with products. Just click on the button below to instantly get back to the shop page. Oh, and while you’re there, check out all of our mind-blowing discounts.</p>
                <div class="d-flex justify-content-center align-content-center py-4 my-4 return-btn">
                    <a href="<?= site_url('/') ?>" class="cart-button">return to shop</a>
                </div>

            <?php else: ?>
                <div class="col-lg-8 col-sm-12">
                    <h3 class="shoping">Shopping Cart</h3>
                    <!-- Desktop View -->
                    <table class="table desktop-view">
                        <tbody>
                            <?php
                            $subtotal = 0;
                            foreach ($cartItems as $item):
                                $itemTotal = $item['price'] * $item['quantity'];
                                $subtotal += $itemTotal;

                                // Handle product images (take first image if multiple)
                                $images = explode(',', $item['product_images']);
                                $firstImage = !empty($images[0]) ? $images[0] : 'default-product-image.jpg';
                            ?>
                                <tr class="woocommerce-cart-form__cart-item cart_item">
                                    <td class="product-remove text-center">
                                        <a href="<?= site_url('cart/remove/' . $item['cart_id']) ?>" class="remove pe-3" style="font-size: 16px;">X</a>
                                    </td>
                                    <td class="product-thumbnail" style="width: 115px;">
                                        <div class="card border border-0 rounded-0" style="background-color: #e8e5e5;">
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <img src="<?= base_url('uploads/products/' . $firstImage); ?>" class="d-block" style="width:90px; height:95px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-name text-center" data-title="Product">
                                        <a href="<?= site_url('product/' . $item['product_id']) ?>"><?= htmlspecialchars($item['product_name']) ?></a>
                                        <div class="product-meta">
                                            <small class="text-muted">
                                                Size: <?= $item['size_in_inches'] ?>",
                                                Color: <?= $item['color_name'] ?>,
                                                <?= $item['touch_name'] ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="product-quantity text-center" data-title="Quantity" style="padding: 0px 60px;">
                                        <form class="update-quantity" method="post">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <div class="input-group quantity-text">
                                                <div class="input-group-prepend d-flex align-items-center">
                                                    <span class="input-group-text ps-3" style="width:100px;color: #929292;background: transparent;">Quantity</span>
                                                </div>
                                                <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['available_quantity'] ?>" style="width: 0px;color: #929292;">
                                            </div>
                                        </form>
                                    </td>
                                    <td class="product-subtotal text-center" data-title="Subtotal" style="color: #929292;font-weight: 600;">
                                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?= number_format($itemTotal, 2) ?></bdi></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Mobile View -->
                    <table class="table table-striped price-table table-bordered mobile-view">
                        <tbody>
                            <?php foreach ($cartItems as $item):
                                $itemTotal = $item['price'] * $item['quantity'];
                                $images = explode(',', $item['product_images']);
                                $firstImage = !empty($images[0]) ? $images[0] : 'default-product-image.jpg';
                            ?>
                                <tr>
                                    <th class="product-remove"><span class="screen-reader-text">Remove item</span></th>
                                    <td class="product-remove text-center">
                                        <a href="<?= site_url('cart/remove/' . $item['cart_id']) ?>" class="remove pe-3" style="font-size: 16px;">X</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="product-thumbnail d-flex"><span class="screen-reader-text">Thumbnail image</span></th>
                                    <td class="product-thumbnail" style="width: 115px;">
                                        <div class="card border border-0 rounded-0" style="background-color: #e8e5e5;">
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <img src="<?= base_url('uploads/products/' . $firstImage); ?>" class="d-block" style="width:90px; height:95px;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="product-name">Product</th>
                                    <td class="product-name text-center" data-title="Product">
                                        <a href="<?= site_url('product/' . $item['product_id']) ?>"><?= htmlspecialchars($item['product_name']) ?></a>
                                        <div class="product-meta">
                                            <small class="text-muted">
                                                Size: <?= $item['size_in_inches'] ?>",
                                                Color: <?= $item['color_name'] ?>,
                                                <?= $item['touch_name'] ?>
                                            </small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="product-price">Price</th>
                                    <td class="product-price text-center" data-title="Price">
                                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?= number_format($item['price'], 2) ?></bdi></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="product-quantity">Quantity</th>
                                    <td class="product-quantity text-center" data-title="Quantity" style="padding: 0px 60px;">
                                        <form class="update-quantity" method="post">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <div class="input-group quantity-text">
                                                <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['available_quantity'] ?>" style="width: 0px;color: #929292;">
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="product-subtotal">Subtotal</th>
                                    <td class="product-subtotal text-center" data-title="Subtotal" style="color: #929292;font-weight: 600;">
                                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span><?= number_format($itemTotal, 2) ?></bdi></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <a class="btn btn-outline-secondary mt-3" href="<?= site_url('/'); ?>">
                        <i class="fa fa-arrow-left pe-2"></i>
                        Continue Shopping
                    </a>
                </div>

                <?php if (!empty($cartItems)): ?>
                    <div class="col-lg-4 col-sm-12 ps-5 mt-4 pt-3 cart-collt">
                        <div class="cart-collaterals">
                            <h2>Cart totals</h2>
                            <div style="border-bottom: 1px solid #e1e1e1;">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fw-bold">Subtotal</div>
                                    <div class="cart-subtotal-value">$<?= number_format($subtotal, 2) ?></div>
                                </div>
                                <div>
                                    <div class="fw-bold mb-2">Shipping</div>
                                    <div class="ps-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="shippingOption" id="freeShipping" value="free" checked>
                                            <label class="form-check-label" for="freeShipping">Free Shipping</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="shippingOption" id="localPickup" value="local">
                                            <label class="form-check-label" for="localPickup">Local Pickup</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="shippingOption" id="flatRate" value="flat">
                                            <label class="form-check-label" for="flatRate">Flat Rate: $10.00</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="cart-subtotal" style="font-size: 16px;">total</div>
                                <div class="total-amt"><strong><span class="woocommerce-Price-amount amount"><bdi><span class="">$</span><?= number_format($subtotal, 2) ?></bdi></span></strong></div>
                            </div>
                            <div class="d-flex justify-content-center align-content-center py-4 w-100">
                                <?php
                                $user_id = session()->get('user_id');
                                $encoded_user_id = base64_encode($user_id);
                                ?>
                                <a href="#" class="cart-button w-100 py-2 text-center" style="font-size: 12px;" id="proceedToCheckout">
                                    Proceed To Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $('#proceedToCheckout').on('click', function(e) {
            e.preventDefault();

            // Get selected shipping option
            const shippingOption = $('input[name="shippingOption"]:checked').val();

            // Base64 encode the shipping option
            const encodedShipping = btoa(shippingOption);

            // Get user ID (already encoded in PHP)
            const encodedUserId = '<?= $encoded_user_id ?>';

            // Redirect to checkout with both parameters
            window.location.href = `<?= site_url('checkout') ?>?uid=${encodedUserId}&ship=${encodedShipping}`;
        });

        $('input[name="quantity"]').on('change', function() {
            const $this = $(this);
            const newQuantity = $this.val();
            const maxQuantity = $this.attr('max');
            const form = $this.closest('form.update-quantity');
            const cartId = form.find('input[name="cart_id"]').val();

            // Validate quantity
            if (newQuantity < 1) {
                $this.val(1);
                return;
            }

            if (parseInt(newQuantity) > parseInt(maxQuantity)) {
                $this.val(maxQuantity);
                $('#modalMessages').html(` 
                    <div class="alert alert-warning alert-dismissible fade show mx-3" role="alert">
                        Maximum available quantity is ${maxQuantity}.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                return;
            }

            // Show loading indicator
            const originalValue = $this.val();
            $this.prop('disabled', true);

            $.ajax({
                url: '<?= site_url('cart/update/') ?>' + cartId,
                method: "POST",
                data: {
                    cart_id: cartId,
                    quantity: newQuantity
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Update the subtotal for this item
                        const itemSubtotal = response.data.itemTotalRaw;
                        const itemRow = form.closest('tr');
                        const mobileItemRow = form.closest('tbody').find(`input[name="cart_id"][value="${cartId}"]`).closest('tr').nextAll().filter(function() {
                            return $(this).find('th').text() === 'Subtotal';
                        }).first();

                        // Update desktop view
                        itemRow.find('.product-subtotal bdi').html('$' + parseFloat(itemSubtotal).toFixed(2));

                        // Update mobile view if exists
                        if (mobileItemRow.length) {
                            mobileItemRow.find('td bdi').html('$' + parseFloat(itemSubtotal).toFixed(2));
                        }

                        // Update cart totals (changed from response.subtotal)
                        $('.cart-subtotal-value').html('$' + parseFloat(response.data.cartTotalRaw).toFixed(2));
                        $('.total-amt bdi').html('$' + parseFloat(response.data.cartTotalRaw).toFixed(2));

                        // Show success message
                        $('#modalMessages').html(` 
                            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                                Quantity updated successfully.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                    } else {
                        // Revert to original value if update failed
                        $this.val(originalValue);

                        if (response.login_url) {
                            $('#myModal').modal('show');
                            $('.nav-tabs a[href="#login"]').tab('show');
                        } else {
                            $('#modalMessages').html(` 
                                <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                                    ${response.message || 'Failed to update quantity. Please try again.'}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $this.val(originalValue);
                    $('#modalMessages').html(` 
                        <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                            An error occurred while updating quantity. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                },
                complete: function() {
                    $this.prop('disabled', false);
                }
            });
        });
        // Handle remove item clicks
        $('.remove').on('click', function(e) {
            e.preventDefault();

            const $this = $(this);
            const removeUrl = $this.attr('href');
            const itemRow = $this.closest('tr');
            const itemCard = $this.closest('.mobile-view tr');

            if (confirm('Are you sure you want to remove this item from your cart?')) {
                $this.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                $.ajax({
                    url: removeUrl,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            $('#modalMessages').html(` 
                                <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            $('.cart-count').text(response.cartCount);

                            // Fade out both desktop and mobile views
                            itemRow.fadeOut(300, function() {
                                $(this).remove();
                            });
                            itemCard.fadeOut(300, function() {
                                $(this).remove();
                            });

                            if (response.cartCount > 0) {
                                $('.total-amt bdi').html('$' + parseFloat(response.cartTotal).toFixed(2));
                                $('.cart-subtotal-value').html('$' + parseFloat(response.cartTotal).toFixed(2));
                            } else if (response.reload) {
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            }
                        } else {
                            if (response.login_url) {
                                $('#myModal').modal('show');
                                $('.nav-tabs a[href="#login"]').tab('show');
                            } else {
                                $('#modalMessages').html(` 
                                    <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                                        ${response.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#modalMessages').html(` 
                            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                                An error occurred while removing the item. Please try again.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>