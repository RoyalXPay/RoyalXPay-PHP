<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Products</li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?= esc($pageTitle); ?></a></li>
                    </ol>

                    <div class="page-title-right">
                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Product Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Product Details</h3>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Product Title:</strong> <?= esc($record->product_name); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Description:</strong> <?= esc($record->description); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Discount:</strong> <?= esc(number_format($record->discount_percentage, 3)); ?>%</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Serial Number:</strong> <?= !empty($record->serial_number) ? esc($record->serial_number) : 'NA'; ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Final Product Price:</strong> <?= esc(number_format($record->final_product_price, 3)); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Quantity:</strong> <?= esc($record->quantity); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Color:</strong> <?= esc($record->color_name); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Size (Inch):</strong> <?= esc($record->size_in_inches); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Glass Type:</strong> <?= esc($record->glass_name); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Touch Type:</strong> <?= esc($record->touch_name); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Resolution Type:</strong> <?= esc($record->resolution_type); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Images Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Product Images</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Images:</strong></p>
                                    <div class="product-images">
                                        <?php
                                        // Handle image display with multiple images
                                        $images = explode(',', esc($record->product_images));
                                        foreach ($images as $image): ?>
                                            <img src="<?= base_url('uploads/products/' . trim($image)); ?>" alt="Product Image" style="max-width: 150px; max-height: 150px; margin-right: 10px;">
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div><!-- End Page-content -->
<?= $this->endSection() ?>