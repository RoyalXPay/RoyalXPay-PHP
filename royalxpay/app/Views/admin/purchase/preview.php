<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Purchases</li>
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
                        <!-- Purchase Summary -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Purchase Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <!-- Purchase Info: ID, Total Amount, Payment Method, Status -->
                                <div class="col-md-4">
                                    <p><strong>Company Name:</strong> <?= esc($record['company_name']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Total Amount:</strong> <?= esc($record['total_amount']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Payment Method:</strong> <?= esc($record['payment_method']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Payment Status:</strong> <?= esc($record['payment_status']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Purchase Status:</strong> <?= esc($record['purchase_status']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($record['uploaded_receipt']): ?>
                                        <p><strong>Receipt:</strong> <a href="<?= base_url('uploads/receipts/' . esc($record['uploaded_receipt'])); ?>" target="_blank">View Attachment</a></p>
                                    <?php else: ?>
                                        <p><strong>Receipt:</strong> No attachment</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Factory / Supplier Product Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Factory / Supplier Product Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Export Wooden Case:</strong> <?= esc($record['export_wooden_case']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Send to Customer's Foshan Warehouse:</strong> <?= esc($record['send_to_warehouse']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Agent Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3 class="text-primary">Agent Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Agent Price:</strong> <?= esc($record['agent_price']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Agent CBM:</strong> <?= esc($record['agent_cbm']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Agent Payment Method:</strong> <?= esc($record['agent_payment_method']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Agent Payment Status:</strong> <?= esc($record['agent_payment_status']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Agent Status:</strong> <?= esc($record['agent_status']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Agent Notes:</strong> <?= esc($record['agent_notes']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($record['agent_attachment']): ?>
                                        <p><strong>Agent Attachment:</strong> <a href="<?= base_url('uploads/agent_attachments/' . esc($record['agent_attachment'])); ?>" target="_blank">View Attachment</a></p>
                                    <?php else: ?>
                                        <p><strong>Agent Attachment:</strong> No attachment</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3 class="text-success">Product Details</h3>
                        </div>
                        <div class="col-md-12">
                            <?= view('admin/_topmessage'); ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">Product Name</th>
                                            <th data-sortable="true" class="text-center">Serial Number</th>
                                            <th data-sortable="true" class="text-center">Size</th>
                                            <th data-sortable="true" class="text-center">Colour</th>
                                            <th data-sortable="true" class="text-center">Glass Type</th>
                                            <th data-sortable="true" class="text-center">Touch/ Non Touch</th>
                                            <th data-sortable="true" class="text-center">Quantity</th>
                                            <th data-sortable="true" class="text-center">Distribute in Rent</th>
                                            <th data-sortable="true" class="text-center">Distribute in Sale</th>
                                            <th data-sortable="true" class="text-center">Available</th>
                                            <th data-sortable="true" class="text-center">Price Per Unit</th>
                                            <th data-sortable="true" class="text-center">CBM</th>
                                            <th data-sortable="true" class="text-center">Shipment Price</th>
                                            <th data-sortable="false" class="text-center">Image</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($purchaseItems as $index => $items): ?>
                                            <tr>
                                                <td class="text-center"><?= esc($items["product_name"]); ?></td>

                                                <!-- Button to open serial number modal -->
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#serialModal<?= $index ?>">
                                                        Show Serial Numbers
                                                    </button>

                                                    <!-- Modal for serial numbers -->
                                                    <div class="modal fade" id="serialModal<?= $index ?>" tabindex="-1" aria-labelledby="serialModalLabel<?= $index ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="serialModalLabel<?= $index ?>">Serial Numbers for <?= esc($items['product_name']) ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <?php if (!empty($items['serialNumbers']) && is_array($items['serialNumbers'])): ?>
                                                                        <ul class="list-group">
                                                                            <?php foreach ($items['serialNumbers'] as $serial): ?>
                                                                                <li class="list-group-item"><?= esc($serial) ?></li>
                                                                            <?php endforeach; ?>
                                                                        </ul>
                                                                    <?php else: ?>
                                                                        <p>No serial numbers available.</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="text-center"><?= esc($items['size_in_inches']); ?> inches</td>
                                                <td class="text-center"><?= esc($items['color_name']); ?></td>
                                                <td class="text-center"><?= esc($items['glass_name']); ?></td>
                                                <td class="text-center"><?= esc($items['touch_name']); ?></td>
                                                <td class="text-center"><?= esc($items['quantity']); ?></td>
                                                <?php
                                                $rent = $sale = $available = 0;
                                                $purchaseId = $items['purchase_id'];
                                                $masterId = $items['product_master_id'];

                                                if (isset($inventorySummaryArray[$purchaseId][$masterId])) {
                                                    $rent = $inventorySummaryArray[$purchaseId][$masterId]['rent'];
                                                    $sale = $inventorySummaryArray[$purchaseId][$masterId]['sale'];
                                                    $available = $inventorySummaryArray[$purchaseId][$masterId]['available'];
                                                }
                                                ?>

                                                <td class="text-center"><?= $rent; ?></td>
                                                <td class="text-center"><?= $sale; ?></td>
                                                <td class="text-center"><?= $available; ?></td>
                                                <td class="text-center"><?= esc($items['price_per_unit']); ?></td>
                                                <td class="text-center"><?= esc($items['cbm_amount']); ?></td>
                                                <td class="text-center"><?= esc($items['shipment_price']); ?></td>
                                                <td class="text-center">
                                                    <?php
                                                    $productImages = explode(',', $items['product_images']);
                                                    $imageUrl = base_url('uploads/products/' . $productImages[0]);
                                                    ?>
                                                    <img src="<?= $imageUrl ?>" alt="<?= esc($items['product_name']) ?>" class="img-fluid" style="max-height: 50px; object-fit: cover;">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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