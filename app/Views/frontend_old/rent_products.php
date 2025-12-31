<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="product-card product-card-con py-5">
        <h2 class="fw-bold text-start pt-5 pb-4 mb-4" style="color: #080808;">Search Rent Items</h2>
        <div class="row d-flex align-items-center justify-content-start search-related-items">
            <form method="get" action="<?= site_url('products/rent') ?>" class="w-100">
                <?= view('flash_messages'); ?>
                <div class="row">
                    <div class="col-lg-3 col-sm-12">
                        <div class="form-group">
                            <label class="mr-sm-2 selection text-dark" for="start_date">Start Date<span class="text-danger ps-1">*</span></label>
                            <div class="form-group mt-1">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= !empty($startDate) ? esc($startDate) : '' ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-12">
                        <div class="form-group">
                            <label class="mr-sm-2 selection text-dark" for="end_date">End Date<span class="text-danger ps-1">*</span></label>
                            <div class="form-group mt-1">
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= !empty($endDate) ? esc($endDate) : '' ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-dark btn-md px-4 py-2 fw-14 fw-normal" style="margin-top: 24px;">Search</button>
                            <a href="<?= site_url('products/rent') ?>" class="btn btn-danger btn-md px-4 py-2 fw-14 fw-normal ms-3" style="margin-top: 24px;">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <h2 class="fw-bold text-start pt-5 mt-5" style="color: #080808;">Related Rent Items</h2>
        <div class="row px-0 pb-5">
            <?php if (!empty($rentProducts)) : ?>
                <?php foreach ($rentProducts as $product) : ?>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-5 mb-4 px-4">
                        <div
                            class="card border border-0 shadow product-card"
                            style="background-color: #e8e5e5; cursor: pointer;"
                            onclick="window.location.href='<?= site_url('products/details/' . $product->product_id); ?>'">
                            <div class="card-header border border-0 pt-3 px-4" style="background-color: #e8e5e5!important;">
                                <div class="d-flex justify-content-end align-items-center">
                                    <span class="mkd-pli-new-product text-primary">View Details</span>
                                </div>
                            </div>
                            <div class="card-body py-4 px-3">
                                <?php
                                $images = explode(',', $product->product_images);
                                $firstImage = !empty($images[0]) ? $images[0] : 'default.png';
                                ?>
                                <img src="<?= base_url('uploads/products/' . $firstImage); ?>" class="d-block mx-auto" style="width:100%; height:500px;" alt="<?= esc($product->product_name); ?>">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                            <h5 class="entry-title mkd-pli-title text-center card-title-name m-0">
                                <a href="<?= site_url('products/details/' . $product->product_id); ?>" class="text-dark text-decoration-none">
                                    <?= esc($product->product_name); ?>
                                </a>
                            </h5>
                            <a href="<?= site_url('products/details/' . $product->product_id); ?>" class="mkd-pli-new-product">View Details</a>
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
<?= $this->endSection() ?>