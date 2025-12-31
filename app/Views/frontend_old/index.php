<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="banner-height" style="background-color: #e8e5e5;">
        <div id="demo" class="carousel slide banners-sliders" data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="true" style="position:relative;">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="d-block">
                        <div class="row d-flex align-items-end justify-content-center p-5">
                            <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6 p-5">
                                <h2 class="px-5">"75" Full Screen Kiosk</h2>
                                <p class="px-5 pb-5">We can help you optimize your cloud strategy & transform your business.</p>
                            </div>
                            <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6 pe-5">
                                <div class="d-flex justify-content-end align-items-center">
                                    <img src="<?= base_url('public/img/FullScreenDigitalsinage.png'); ?>" alt="Los Angeles">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row d-flex align-items-end justify-content-center p-5">
                        <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6 p-5">
                            <h2 class="px-5">"75" Floor Standing</h2>
                            <p class="px-5 pb-5">We can help you optimize your cloud strategy & transform your business.</p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6 pe-5">
                            <div class="d-flex justify-content-end align-items-center">
                                <img src="<?= base_url('public/img/75_FloorStanding.png'); ?>" alt="Los Angeles">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row d-flex align-items-end justify-content-center p-5">
                        <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6 p-5">
                            <h2 class="px-5">"65" Floor Standing</h2>
                            <p class="px-5 pb-5">We can help you optimize your cloud strategy & transform your business.</p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6 pe-5">
                            <div class="d-flex justify-content-end align-items-center">
                                <img src="<?= base_url('public/img/65_FloorStanding.png'); ?>" alt="Los Angeles">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    <div class="py-5 product-card">
        <div class="row px-0 pb-5 pt-4  d-flex align-items-center justify-content-center">
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-4 mb-4">
                <a href="<?= site_url('products/rent'); ?>">
                    <div class="card border border-0 shadow" style="background-color: #e8e5e5; height: 200px;">
                        <div class="card-body py-5 d-flex align-items-center justify-content-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <h2 class="fw-bold text-center">FOR RENT</h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-4 mb-4 ms-4">
                <a href="<?= site_url('products/sale'); ?>">
                    <div class="card border border-0 shadow" style="background-color: #e8e5e5; height: 200px;">
                        <div class="card-body py-5 d-flex align-items-center justify-content-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <h2 class="fw-bold text-center">FOR SALE</h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>