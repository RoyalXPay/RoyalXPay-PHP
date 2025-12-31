<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-form {
        display: none;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .search-form.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    #datatable td, #datatable th {
    white-space: nowrap;      /* Prevent text wrapping */
    overflow: hidden;         /* Hide overflow */
    text-overflow: ellipsis;  /* Show ... if text too long */
    vertical-align: middle;
}

/* Force action button to fit */
#datatable td button {
    width: 100%;
    max-width: 100px; /* adjust button width */
    white-space: nowrap;
}


  .transaction-details {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
  }

  .detail-row {
    display: flex;
    padding: 10px 15px;
    border-bottom: 1px solid #dee2e6;
  }

  .detail-row:last-child {
    border-bottom: none;
  }

  .detail-row .label {
    flex: 0 0 30%;
    font-weight: 600;
    background: #f8f9fa;
    padding: 6px 10px;
    border-radius: 4px;
    margin-right: 10px;
  }

  .detail-row .value {
    flex: 1;
    padding: 6px 10px;
  }


</style>


<div class="page-content adj">
     <div class="container-fluid">
        
    <h4 class="mb-4">Transaction Reports</h4>

    <!-- Filters -->
    <form method="get" action="">
        <div class="row mb-3">
            <div class="col-md-2"><input type="date" name="start_date" value="<?= esc($filters['start_date'] ?? '') ?>" class="form-control" placeholder="Start Date"></div>
            <div class="col-md-2"><input type="date" name="end_date" value="<?= esc($filters['end_date'] ?? '') ?>" class="form-control" placeholder="End Date"></div>
            <div class="col-md-2"><input type="text" name="username" value="<?= esc($filters['username'] ?? '') ?>" class="form-control" placeholder="Username"></div>
            <div class="col-md-2"><input type="text" name="mobile" value="<?= esc($filters['mobile'] ?? '') ?>" class="form-control" placeholder="Mobile"></div>
            <div class="col-md-2"><input type="text" name="email" value="<?= esc($filters['email'] ?? '') ?>" class="form-control" placeholder="Email"></div>
            <div class="col-md-2"><input type="text" name="merchant" value="<?= esc($filters['merchant'] ?? '') ?>" class="form-control" placeholder="Merchant Name"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-2"><input type="text" name="txn_id" value="<?= esc($filters['txn_id'] ?? '') ?>" class="form-control" placeholder="Transaction ID"></div>
            <div class="col-md-2">
                <select name="Payment_type" class="form-control">
                    <option value="">All Bill Types</option>
                    <option value="AADC" <?= !empty($filters['Payment_type']) && $filters['Payment_type']=='AADC'?'selected':'' ?>>AADC</option>
                    <option value="ADDC" <?= !empty($filters['Payment_type']) && $filters['Payment_type']=='ADDC'?'selected':'' ?>>ADDC</option>
                    <option value="DEWA" <?= !empty($filters['Payment_type']) && $filters['Payment_type']=='DEWA'?'selected':'' ?>>DEWA</option>
                    <option value="Sergas" <?= !empty($filters['Payment_type']) && $filters['Payment_type']=='Sergas'?'selected':'' ?>>Sergas</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary">Search</button>
        <a href="<?= site_url('admin/transaction-report') ?>" class="btn btn-secondary">Clear</a>
        <br /><br />
    </form>

    <!-- Transaction Table -->
     <div class="table-responsive">
    <table  id="datatable" class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Txn ID</th>
                <th>Account No</th>
                <th>Type</th>
                <th>Date/Time</th>
                <th>Commission</th>
                <th>Payable</th>
                <th>Merchant</th>
                <th>Done By</th>
                <th>Status</th>
                <th>Customer Mobile</th>
                <th>Wallet Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            $totalCommission = 0;
            $totalPayable = 0;
        ?>
        <?php if(!empty($transactions)): ?>
            <?php foreach($transactions as $txn): ?>
                <?php 
                    $totalCommission += (float)$txn['commision'];
                    $totalPayable += (float)$txn['amount'];
                ?>
                <tr>
                    <td><?= esc($txn['transaction_id']) ?></td>
                    <td><?= esc($txn['account_number']) ?></td>
                    <td><?= esc($txn['Payment_type']) ?></td>
                    <td><?= esc($txn['created_at']) ?></td>
                    <td><?= esc(number_format((float)$txn['commision'], 2)) ?></td>
                    <td><?= esc(number_format((float)$txn['amount'], 2)) ?></td>
                    <td><?= esc($txn['name']) ?></td>
                    <td><?= esc($txn['name']) ?></td>
                    <td><?= esc($txn['status']) ?></td>
                    <td><?= esc($txn['customer_mobile']) ?></td>
                    <td><?= esc(number_format((float)$txn['wallet'], 2)) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#txnModal<?= $txn['id'] ?>"> <i class="fas fa-eye"></i></button>
                    </td>
                </tr>

               <!-- Modal for this row -->
<div class="modal fade" id="txnModal<?= $txn['id'] ?>" tabindex="-1" aria-labelledby="txnModalLabel<?= $txn['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="txnModalLabel<?= $txn['id'] ?>">
          Transaction Details - <?= esc($txn['transaction_id']) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">

        <div class="transaction-details">
          <div class="detail-row">
            <span class="label">Txn ID</span>
            <span class="value"><?= esc($txn['transaction_id']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Account No</span>
            <span class="value"><?= esc($txn['account_number']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Type</span>
            <span class="value"><?= esc($txn['Payment_type']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Date/Time</span>
            <span class="value"><?= esc($txn['created_at']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Commission</span>
            <span class="value"><?= esc(number_format((float)$txn['commision'], 2)) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Payable</span>
            <span class="value"><?= esc(number_format((float)$txn['amount'], 2)) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Merchant</span>
            <span class="value"><?= esc($txn['name']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Done By</span>
            <span class="value"><?= esc($txn['name']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Status</span>
            <span class="value"><?= esc($txn['status']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Customer Mobile</span>
            <span class="value"><?= esc($txn['customer_mobile']) ?></span>
          </div>
          <div class="detail-row">
            <span class="label">Wallet Balance</span>
            <span class="value"><?= esc(number_format((float)$txn['wallet'], 2)) ?></span>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

             <?php endforeach; ?>
<?php endif; ?>
        </tbody>
         <!-- ✅ Totals in tfoot -->
 <?php if(!empty($transactions)): ?>
<tfoot>
    <tr class="table-secondary fw-bold">
        <td colspan="4" class="text-end">Total:</td>
        <td><?= esc(number_format($totalCommission, 2)) ?></td>
        <td><?= esc(number_format($totalPayable, 2)) ?></td>
        <td colspan="5"></td> <!-- ✅ keep merchant, done by, status, mobile, wallet -->
        <td></td> <!-- ✅ empty Action column -->
    </tr>
</tfoot>
<?php endif; ?>
    </table>



    </div>
</div>

</div>

<script>
   $(document).ready(function() {
    $('#datatable').DataTable({
        dom: 'Bfrtip',
        searching: true,
        paging: true,
        info: false,
    });
});
</script>
<?= $this->endSection(); ?>
