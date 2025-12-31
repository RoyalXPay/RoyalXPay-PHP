<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Orders</li>
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
                        <!-- Order Summary -->
                        <div class="col-12 border-bottom mb-4">
                            <h3>Order Details</h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Order ID:</strong> #<?= esc($order['order_id']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Total Amount:</strong> $<?= number_format($order['total'], 2); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Order Status:</strong>
                                        <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : ($order['status'] == 'cancelled' ? 'danger' : 'info')) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Customer Name:</strong> <?= esc($order['name'] ?? 'Guest'); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Company:</strong> <?= esc($order['company'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Email:</strong> <?= esc($order['email']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Phone:</strong> <?= esc($order['phone'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Order Date:</strong> <?= date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                                </div>
                                <div class="col-md-12">
                                    <p><strong>Notes:</strong> <?= esc($order['notes'] ?? 'No notes'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Section -->
                        <div class="col-12 border-bottom mb-4">
                            <h3 class="text-success">Order Items</h3>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Product Name</th>
                                            <th class="text-center">Size</th>
                                            <th class="text-center">Color</th>
                                            <th class="text-center">Glass Type</th>
                                            <th class="text-center">Touch Type</th>
                                            <th class="text-center">Resolution</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Unit Price</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($items as $item) { ?>
                                            <tr>
                                                <td class="text-center"><?= esc($item['product_name']); ?></td>
                                                <td class="text-center"><?= esc($item['size_name']); ?></td>
                                                <td class="text-center"><?= esc($item['color_name']); ?></td>
                                                <td class="text-center"><?= esc($item['glass_name']); ?></td>
                                                <td class="text-center"><?= esc($item['touch_name']); ?></td>
                                                <td class="text-center"><?= esc($item['resolution_name']); ?></td>
                                                <td class="text-center"><?= esc($item['quantity']); ?></td>
                                                <td class="text-center">$<?= number_format($item['price'], 2); ?></td>
                                                <td class="text-center">$<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" class="text-end"><strong>Subtotal:</strong></td>
                                            <td colspan="2" class="text-center">$<?= number_format($order['subtotal'] ?? $order['total'], 2); ?></td>
                                        </tr>
                                        <?php if (!empty($order['tax']) && $order['tax'] > 0) : ?>
                                            <tr>
                                                <td colspan="8" class="text-end"><strong>Tax:</strong></td>
                                                <td colspan="2" class="text-center">$<?= number_format($order['tax'], 2); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($order['shipping_cost']) && $order['shipping_cost'] > 0) : ?>
                                            <tr>
                                                <td colspan="8" class="text-end"><strong>Shipping:</strong></td>
                                                <td colspan="2" class="text-center">$<?= number_format($order['shipping_cost'], 2); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($order['discount']) && $order['discount'] > 0) : ?>
                                            <tr>
                                                <td colspan="8" class="text-end"><strong>Discount:</strong></td>
                                                <td colspan="2" class="text-center">-$<?= number_format($order['discount'], 2); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td colspan="8" class="text-end"><strong>Total:</strong></td>
                                            <td colspan="2" class="text-center"><strong>$<?= number_format($order['total'], 2); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <!-- <div class="col-12 mt-4">
                            <div class="d-flex justify-content-end">
                                <a href="</?= site_url('admin/orders/print/' . $order['order_id']) ?>" class="btn btn-info me-2" target="_blank">
                                    <i class="mdi mdi-printer"></i> Print Invoice
                                </a>
                                </?php if ($order['status'] == 'pending') : ?>
                                    <button class="btn btn-success me-2" id="markAsCompleted" data-id="</?= $order['order_id'] ?>">
                                        <i class="mdi mdi-check"></i> Mark as Completed
                                    </button>
                                    <button class="btn btn-danger" id="cancelOrder" data-id="</?= $order['order_id'] ?>">
                                        <i class="mdi mdi-cancel"></i> Cancel Order
                                    </button>
                                </?php endif; ?>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- <script>
    $(document).ready(function() {
        // Mark as completed
        $('#markAsCompleted').click(function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'completed');
        });

        // Cancel order
        $('#cancelOrder').click(function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'cancelled');
        });

        function updateOrderStatus(orderId, status) {
            if (confirm('Are you sure you want to update this order status?')) {
                $.post('<?= site_url('admin/orders/update_status') ?>', {
                    order_id: orderId,
                    status: status,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Error updating order status');
                    }
                }, 'json');
            }
        }
    });
</script> -->
<?= $this->endSection() ?>