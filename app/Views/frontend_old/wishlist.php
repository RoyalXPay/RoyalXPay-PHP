<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid cart-container">
    <h2>Wishlist</h2>
</div>
<div class="container">

    <div class="wishlist-container py-5 my-5">
        <table class="table desktop-view">
            <thead style="display: none;">
                <tr>
                    <th class="product-remove">
                        <span class="screen-reader-text">Remove item</span>
                    </th>
                    <th class="product-thumbnail">
                        <span class="screen-reader-text">Thumbnail image</span>
                    </th>
                    <th class="product-name">Product</th>
                    <th class="product-price">Price</th>
                    <th class="product-subtotal">Stock</th>
                    <th class="product-quantity">Cart</th>
                </tr>
            </thead>
            <tbody>
                <tr class="woocommerce-cart-form__cart-item cart_item">
                    <td class="product-remove text-center">
                        <a href="#" class="remove pe-3" style="font-size: 16px;">X</a>
                    </td>
                    <td class="product-thumbnail" style="width: 115px;">
                        <div class="card border border-0 rounded-0" style="background-color: #e8e5e5;">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <img src="<?= base_url('public/img/75_FloorStanding.png'); ?>" class="d-block" style="width:90px; height:95px;">
                            </div>
                        </div>
                    </td>
                    <td class="product-name text-start" data-title="Product">
                        <a href="#">"75" Floor Standing</a>
                    </td>
                    <td class="product-price text-center" data-title="Price">
                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>160</bdi></span>
                    </td>
                    <td class="product-price text-end" data-title="Price">
                        <span class="woocommerce-Price-amount amount" style="font-size: 14px; font-weight: 500;color: #929292;">Instock</span>
                    </td>
                    <td class="product-price text-end" data-title="Price">
                        <a href="<?= site_url('cart'); ?>" class="cart-subtotal" style="font-size: 13px;">ADD TO CART</a>
                    </td>
                </tr>
                <tr class="woocommerce-cart-form__cart-item cart_item">
                    <td class="product-remove text-center">
                        <a href="#" class="remove pe-3" style="font-size: 16px;">X</a>
                    </td>
                    <td class="product-thumbnail" style="width: 115px;">
                        <div class="card border border-0 rounded-0" style="background-color: #e8e5e5;">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <img src="<?= base_url('public/img/75_FloorStanding.png'); ?>" class="d-block" style="width:90px; height:95px;">
                            </div>
                        </div>
                    </td>
                    <td class="product-name text-start" data-title="Product">
                        <a href="#">"75" Floor Standing</a>
                    </td>
                    <td class="product-price text-center" data-title="Price">
                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>160</bdi></span>
                    </td>
                    <td class="product-price text-end" data-title="Price">
                        <span class="woocommerce-Price-amount amount" style="font-size: 14px; font-weight: 500;color: #929292;">Instock</span>
                    </td>
                    <td class="product-price text-end" data-title="Price">
                        <a href="<?= site_url('cart'); ?>" class="cart-subtotal" style="font-size: 13px;">ADD TO CART</a>
                    </td>
                </tr>
                <tr class="woocommerce-cart-form__cart-item cart_item">
                    <td class="product-remove text-center">
                        <a href="#" class="remove pe-3" style="font-size: 16px;">X</a>
                    </td>
                    <td class="product-thumbnail" style="width: 115px;">
                        <div class="card border border-0 rounded-0" style="background-color: #e8e5e5;">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <img src="<?= base_url('public/img/75_FloorStanding.png'); ?>" class="d-block" style="width:90px; height:95px;">
                            </div>
                        </div>
                    </td>
                    <td class="product-name text-start" data-title="Product">
                        <a href="#">"75" Floor Standing</a>
                    </td>
                    <td class="product-price text-center" data-title="Price">
                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>160</bdi></span>
                    </td>
                    <td class="product-price text-end" data-title="Price">
                        <span class="woocommerce-Price-amount amount" style="font-size: 14px; font-weight: 500;color: #929292;">Instock</span>
                    </td>
                    <td class="product-price text-end" data-title="Price">
                        <a href="<?= site_url('cart'); ?>" class="cart-subtotal" style="font-size: 13px;">ADD TO CART</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-striped price-table table-bordered mobile-view">
            <tbody>
                <tr>
                    <th class="product-remove"><span class="screen-reader-text">Remove item</span></th>
                    <td class="product-remove text-center">
                        <a href="#" class="remove pe-3" style="font-size: 16px;">X</a>
                    </td>
                </tr>
                <tr>
                    <th class="product-thumbnail d-flex"><span class="screen-reader-text">Thumbnail image</span></th>
                    <td class="product-thumbnail" style="width: 115px;">
                        <div class="card border border-0 rounded-0" style="background-color: #e8e5e5;">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <img src="<?= base_url('public/img/75_FloorStanding.png'); ?>" class="d-block" style="width:90px; height:95px;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="product-name">Product</th>
                    <td class="product-name text-center" data-title="Product">
                        <a href="#">"75" Floor Standing</a>
                    </td>
                </tr>
                <tr>
                    <th class="product-price">Price</th>
                    <td class="product-price text-center" data-title="Price">
                        <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>160</bdi></span>
                    </td>
                </tr>
                <tr>
                    <th class="product-quantity">Quantity</th>
                    <td class="product-price text-end" data-title="Price">
                        <span class="woocommerce-Price-amount amount" style="font-size: 14px; font-weight: 500;color: #929292;">Instock</span>
                    </td>
                </tr>
                <tr>
                    <th class="product-subtotal">Subtotal</th>
                    <td class="product-price text-end" data-title="Price">
                        <a href="<?= site_url('cart'); ?>" class="cart-subtotal" style="font-size: 13px;">ADD TO CART</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>