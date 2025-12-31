<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Quotations</li>
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
                        <!-- Quotation Summary -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Quotation Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <!-- Quotation Info: ID, Code, Dates, Price, Customer -->
                                <div class="col-md-4">
                                    <p><strong>Quotation ID:</strong> <?= esc($record['quotation_code']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Quotation Date:</strong> <?= esc($record['quotation_date']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Customer Name:</strong> <?= esc($record['customer_name']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Phone:</strong> <?= esc($record['phone']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Total Amount:</strong> <?= esc($record['total_price']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>VAT:</strong> <?= esc($record['vat']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Final Payable Amount:</strong> <?= esc($record['final_payable_price']); ?></p>
                                </div>
                                <div class="col-md-12">
                                    <p><strong>Notes:</strong> <?= esc($record['notes']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3 class="text-success">Product Details</h3>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">Product Name</th>
                                            <th data-sortable="true" class="text-center">Serial Number</th>
                                            <th data-sortable="true" class="text-center">Size</th>
                                            <th data-sortable="true" class="text-center">Colour</th>
                                            <th data-sortable="true" class="text-center">Touch Type</th>
                                            <th data-sortable="true" class="text-center">Glass Type</th>
                                            <th data-sortable="true" class="text-center">Quantity</th>
                                            <th data-sortable="true" class="text-center">Price</th>
                                            <th data-sortable="true" class="text-center">Discount</th>
                                            <th data-sortable="false" class="text-center">Image</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($record['quotationProducts'] as $item) { ?>
                                            <tr>
                                                <td class="text-center"><?= esc($item["product_name"]); ?></td>
                                                <td class="text-center"><?= esc($item["serial_number"]); ?></td>
                                                <td class="text-center"><?= esc($item['size_in_inches']); ?> inches</td>
                                                <td class="text-center"><?= esc($item['color_name']); ?></td>
                                                <td class="text-center"><?= esc($item['touch_name']); ?></td>
                                                <td class="text-center"><?= esc($item['glass_name']); ?></td>
                                                <td class="text-center"><?= esc($item['quantity']); ?></td>
                                                <td class="text-center"><?= esc($item['price']); ?></td>
                                                <td class="text-center"><?= esc($item['discount']); ?></td>
                                                <td class="text-center">
                                                    <?php
                                                    $productImages = explode(',', $item['product_images']);
                                                    $imageUrl = base_url('uploads/products/' . $productImages[0]);
                                                    ?>
                                                    <img src="<?= $imageUrl ?>" alt="<?= $item['product_name'] ?>" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div><!-- End Page-content -->
<?= $this->endSection() ?>