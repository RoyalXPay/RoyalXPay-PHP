<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php

$productImages = explode(',', esc($productDetails->product_images));
$mainImage = base_url('uploads/products/' . esc($productImages[0]));
?>

<div class="container">
    <div class="details-product py-5 my-5">
        <div class="row pb-5">
            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12 pe-5">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 col-md-6 col-sm-6">
                        <div class="d-flex flex-column">
                            <?php
                            $totalToShow = 4;
                            $imagesShown = 0;
                            $defaultImage = base_url('assets/images/default.jpg');

                            foreach ($productImages as $image):
                                if ($imagesShown >= $totalToShow) break;
                                $imagePath = 'uploads/products/' . esc($image);
                                if (file_exists(FCPATH . $imagePath)) :
                                    $imagesShown++;
                                    $thumbImage = base_url($imagePath); ?>
                                    <div class="card border border-0 shadow rounded-0 mt-3 thumbnail-img"
                                        style="background-color: #e8e5e5; cursor: pointer;"
                                        data-image="<?= $thumbImage ?>">
                                        <div class="card-body py-4 px-4">
                                            <img src="<?= $thumbImage ?>" class="d-block mx-auto" style="width:90px; height:95px;" alt="Product thumbnail">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Show default images if less than 4 -->
                            <?php for ($i = $imagesShown; $i < $totalToShow; $i++): ?>
                                <div class="card border border-0 shadow rounded-0 mt-3" style="background-color: #e8e5e5;">
                                    <div class="card-body py-4 px-4">
                                        <img src="<?= esc($defaultImage); ?>" class="d-block mx-auto" style="width:90px; height:95px;" alt="Default product image">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="col-lg-9 col-xl-9 col-md-6 col-sm-6 high-img">
                        <div class="card border border-0 shadow rounded-0" style="background-color: #e8e5e5;">
                            <div class="card-body py-5">
                                <img id="mainProductImage" src="<?= esc($mainImage); ?>" class="d-block mx-auto" style="width:100%; height:550px;" alt="Main product image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12 px-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 itemprop="name" class="mkd-single-product-title m-0"><?= esc($productDetails->product_name) ?></h3>
                </div>
                <table class="table table-striped price-table table-bordered table-hover price-table-input desktop-view">
                    <thead>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Size <br>(inches)
                                </span>
                            </th>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    glass <br> or Mirror glass
                                </span>
                            </th>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Touch or <br>Non touch
                                </span>
                            </th>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Color
                                </span>
                            </th>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Price <br> per day
                                </span>
                            </th>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Qty
                                </span>
                            </th>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Details
                                </span>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->size_in_inches); ?>
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->glass_name); ?>
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->touch_name); ?>
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->color_name); ?>
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->price_per_day); ?> OMR
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->available_quantity); ?>
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <a class="text-center tablinks" onclick="switchProductTab(event, 'additional-info-tab')">
                                        <i class="fa fa-info-circle" style="font-size:20px;color:red"></i>
                                    </a>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-striped price-table table-bordered mobile-view">
                    <tbody>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Size <br>(inches)
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->size_in_inches); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Tempered glass <br> or Mirror glass
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->glass_name); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Touch or <br>Non touch
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->touch_name); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Color
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->color_name); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Price <br> per day
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->price_per_day); ?> OMR
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Qty
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <?= esc($productDetails->available_quantity); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    Details
                                </span>
                            </th>
                            <td>
                                <span class="d-flex align-items-center justify-content-center align-self-center text-center">
                                    <a class="text-center tablinks detail-view" onclick="switchProductTab(event, 'additional-info-tab')">
                                        <i class="fa fa-info-circle" style="font-size:20px;color:red"></i>
                                    </a>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="my-4 py-4 description">
                    <ul class="d-flex justify-content-start align-items-center flex-nowrap px-0 mb-0" role="tablist" style="border: none!important;">
                        <li class="nav-item">
                            <a class="tablinks d-flex" onclick="switchProductTab(event, 'product-description-tab')" id="default-tab-link">Product description</a>
                        </li>
                        <li class="nav-item">
                            <a class="tablinks" onclick="switchProductTab(event, 'additional-info-tab')" id="additional-info-link">Additional Information</a>
                        </li>
                    </ul>
                    <div class="mb-2 product-info p-4" style="border: 1px solid #e1e1e1;">
                        <div id="product-description-tab" class="tabcontent">
                            <p style="text-align: justify;">
                                <?= esc($productDetails->description); ?>
                            </p>
                        </div>
                        <div id="additional-info-tab" class="tabcontent" style="display:none;">
                            <table class="table table-striped price-table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Brightness</th>
                                        <td>450 cd/m2</td>
                                    </tr>
                                    <tr>
                                        <th>Resolution</th>
                                        <td>1080*1920 Hd</td>
                                    </tr>
                                    <tr>
                                        <th>Operation system</th>
                                        <td>Android 9.0</td>
                                    </tr>
                                    <tr>
                                        <th>Motherboard</th>
                                        <td>T972 4gb RAM + 32gb ROM with WIFI , USB , Bluetooth , HDMI in</td>
                                    </tr>
                                    <tr>
                                        <th>Color</th>
                                        <td>Black</td>
                                    </tr>
                                    <tr>
                                        <th>Language</th>
                                        <td>English</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <form class="add-to-cart-form" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= esc($productDetails->product_id) ?>">
                        <input type="hidden" name="price" value="<?= esc($productDetails->price_per_day) ?>">

                        <div class="input-group quantity-text">
                            <div class="input-group-prepend d-flex align-items-center">
                                <span class="input-group-text">Quantity</span>
                            </div>
                            <input type="number" name="quantity" class="form-control quantity-input" value="1" placeholder="1" min="1">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-dark text-white text-uppercase px-4" style="height: 100%; border-radius: 0;">
                                    Add to cart
                                </button>
                            </div>
                        </div>
                    </form>
                    <a href="<?= site_url('wishlist'); ?>" class="cart"><i class="fa fa-heart pe-2"></i>Add to wishlist</a>
                </div>
            </div>
        </div>

        <h2 class="fw-bold text-start pt-5 mt-5 related-product-heading" style="color: #080808;">Related Products</h2>
        <div class="row px-0 pb-5 related-detail-img">
            <?php if (!empty($rentProducts)) : ?>
                <?php foreach ($rentProducts as $product) :
                    if ($product->product_id === $productDetails->product_id) {
                        continue;
                    }
                ?>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-5 mb-4 px-4">
                        <div
                            class="card border border-0 shadow product-card"
                            style="background-color: #e8e5e5; cursor: pointer;"
                            onclick="window.location.href='<?= site_url('products/details/' . esc($product->product_id, 'url')); ?>'">
                            <div class="card-header border border-0 pt-3 px-4" style="background-color: #e8e5e5!important;">
                                <div class="d-flex justify-content-end align-items-center">
                                    <span class="mkd-pli-new-product text-primary">View Details</span>
                                </div>
                            </div>
                            <div class="card-body py-4 px-3">
                                <?php
                                $images = explode(',', esc($product->product_images));
                                $firstImage = !empty($images[0]) ? $images[0] : 'default.png';
                                ?>
                                <img src="<?= base_url('uploads/products/' . esc($firstImage)); ?>" class="d-block mx-auto" style="width:100%; height:500px;" alt="<?= esc($product->product_name); ?>">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                            <h5 class="entry-title mkd-pli-title text-center card-title-name m-0">
                                <a href="<?= site_url('products/details/' . esc($product->product_id, 'url')); ?>" class="text-dark text-decoration-none">
                                    <?= esc($product->product_name); ?>
                                </a>
                            </h5>
                            <a href="<?= site_url('products/details/' . esc($product->product_id, 'url')); ?>" class="mkd-pli-new-product">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="text-center text-muted">No rent items found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all thumbnail elements
        const thumbnails = document.querySelectorAll('.thumbnail-img');

        // Add click event to each thumbnail
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Get the image URL from data attribute
                const newImageUrl = this.getAttribute('data-image');

                // Update the main image
                if (newImageUrl) {
                    document.getElementById('mainProductImage').src = newImageUrl;
                }
            });
        });
    });

    /**
     * Switches between product information tabs
     * @param {Event} event - The click event
     * @param {string} tabId - The ID of the tab to show
     */
    function switchProductTab(event, tabId) {
        event.preventDefault();

        // Hide all tab contents
        const tabContents = document.getElementsByClassName("tabcontent");
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].style.display = "none";
        }

        // Remove active class from all tab links
        const tabLinks = document.getElementsByClassName("tablinks");
        for (let i = 0; i < tabLinks.length; i++) {
            tabLinks[i].classList.remove("active");
        }

        // Show the selected tab
        document.getElementById(tabId).style.display = "block";

        // Add active class to the corresponding nav link (if exists)
        if (tabId === 'product-description-tab') {
            document.getElementById('default-tab-link').classList.add('active');
        } else if (tabId === 'additional-info-tab') {
            document.getElementById('additional-info-link').classList.add('active');
        }
    }

    // Initialize tabs on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Show the first tab by default
        document.getElementById('product-description-tab').style.display = "block";
        document.getElementById('default-tab-link').classList.add("active");

        // Set up quantity input validation - Option 1: using [0]
        const quantity = document.getElementsByClassName('quantity-input')[0];
        quantity.addEventListener('input', function() {
            if (this.value < 1) {
                this.value = 1;
            }
        });
    });

    $('.add-to-cart-form').on('submit', function(e) {
        e.preventDefault();

        // Remove any previous alerts
        $('.add-to-cart-form .alert').remove();

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            url: "<?= site_url('cart/add') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    form.prepend(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);

                    // Update cart counters in navbar.
                    $('.cart-count').text(response.cartCount);

                } else {
                    if (response.login_url) {
                        // Show login modal if not logged in
                        $('#myModal').modal('show');

                        // Optional: Set the login tab as active
                        $('.nav-tabs a[href="#login"]').tab('show');
                    } else if (response.errors) {
                        // Validation errors
                        let errorMessages = '<ul>';
                        for (let key in response.errors) {
                            errorMessages += `<li>${response.errors[key]}</li>`;
                        }
                        errorMessages += '</ul>';

                        form.prepend(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ${errorMessages}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    } else {
                        form.prepend(`
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    }
                }
            },
            error: function() {
                form.prepend(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Something went wrong. Please try again later.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
            }
        });
    });
</script>
<?= $this->endSection() ?>