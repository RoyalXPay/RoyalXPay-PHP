<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0"><?= esc($title) ?></h4>
                    <div class="page-title-right">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNorkaModal">
                            <i class="mdi mdi-plus me-1"></i> Add Norka Customer
                        </button>
                        <!-- <a href="<?= site_url('admin/norka/roots') ?>" target="_blank" class="btn btn-success">
    <i class="mdi mdi-web me-1"></i> Norkaroots Website
</a> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="get">
    <div class="row g-3">
        <div class="col-md-3">
            <input type="text" name="keyword" value="<?= esc($_GET['keyword'] ?? '') ?>" class="form-control" placeholder="Search by name, email, mobile...">
        </div>
        <div class="col-md-3">
            <input type="date" name="start_date" value="<?= esc($_GET['start_date'] ?? '') ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="date" name="end_date" value="<?= esc($_GET['end_date'] ?? '') ?>" class="form-control">
        </div>
        <div class="col-md-3 d-flex">
            <button type="submit" class="btn btn-outline-primary me-2">
                <i class="mdi mdi-magnify me-1"></i> Search
            </button>
            <a href="<?= current_url() ?>" class="btn btn-light">Reset</a>
        </div>
    </div>
</form>

            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="norkaTable"  class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($results)): ?>
                                <?php $i=1; foreach($results as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <?php if($row['image']): ?>
                                            <img src="<?= base_url('uploads/norka/'.$row['image']) ?>" width="40" height="40" class="rounded-circle">
                                        <?php else: ?>
                                            <span class="text-muted">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($row['first_name'].' '.$row['last_name']) ?></td>
                                    <td><?= esc($row['role']) ?></td>
                                    <td><?= esc($row['dob']) ?></td>
                                    <td><?= esc($row['gender']) ?></td>
                                    <td><?= esc($row['mobile']) ?></td>
                                    <td><?= esc($row['email']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td class="text-center">
                                         <button class="btn btn-sm btn-info" 
            data-bs-toggle="modal" 
            data-bs-target="#editNorkaModal<?= $row['id'] ?>">
        <i class="fas fa-edit"></i>
    </button>
                                        <form action="<?= site_url('admin/norka/delete/'.$row['id']) ?>" method="post" onsubmit="return confirm('Delete this customer?');">
                                           
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="10" class="text-center text-muted">No records found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addNorkaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/norka/save') ?>" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Norka Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <input type="hidden" name="type" value="<?= (strpos($title,'Insurance')!==false)?'insurance':'care' ?>">

                <div class="col-md-4">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="">Select</option>
                        <option>NRK Abroad</option>
                        <option>NRK Other States</option>
                        <option>Returnees</option>
                        <option>Keralites</option>
                        <option>Job Aspirant</option>
                        <option>Student</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Date of Birth *</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Gender *</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Transgender</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Profile Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="col-md-6">
                    <label>Mobile *</label>
                    <input type="text" name="mobile" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="col-md-6">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Customer</button>
            </div>
        </form>
    </div>
</div>


<?php foreach($results as $row): ?>
<div class="modal fade" id="editNorkaModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" 
              action="<?= site_url('admin/norka/update/'.$row['id']) ?>" 
              enctype="multipart/form-data" 
              class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">Edit Norka Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body row g-3">
                <input type="hidden" name="old_image" value="<?= esc($row['image']) ?>">

                <div class="col-md-4">
                    <label>First Name *</label>
                    <input type="text" name="first_name" value="<?= esc($row['first_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" value="<?= esc($row['middle_name']) ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" value="<?= esc($row['last_name']) ?>" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Role *</label>
                    <select name="role" class="form-select" required>
                        <option <?= ($row['role']=='NRK Abroad')?'selected':'' ?>>NRK Abroad</option>
                        <option <?= ($row['role']=='NRK Other States')?'selected':'' ?>>NRK Other States</option>
                        <option <?= ($row['role']=='Returnees')?'selected':'' ?>>Returnees</option>
                        <option <?= ($row['role']=='Keralites')?'selected':'' ?>>Keralites</option>
                        <option <?= ($row['role']=='Job Aspirant')?'selected':'' ?>>Job Aspirant</option>
                        <option <?= ($row['role']=='Student')?'selected':'' ?>>Student</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Date of Birth *</label>
                    <input type="date" name="dob" value="<?= esc($row['dob']) ?>" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Gender *</label>
                    <select name="gender" class="form-select" required>
                        <option <?= ($row['gender']=='Male')?'selected':'' ?>>Male</option>
                        <option <?= ($row['gender']=='Female')?'selected':'' ?>>Female</option>
                        <option <?= ($row['gender']=='Transgender')?'selected':'' ?>>Transgender</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Profile Image</label><br>
                    <?php if($row['image']): ?>
                        <img src="<?= base_url('uploads/norka/'.$row['image']) ?>" width="40" class="rounded mb-2">
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="col-md-6">
                    <label>Mobile *</label>
                    <input type="text" name="mobile" value="<?= esc($row['mobile']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= esc($row['phone']) ?>" class="form-control">
                </div>

                <div class="col-md-6">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= esc($row['email']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>New Password (leave blank to keep)</label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update Customer</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
    $('#norkaTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-primary' },
            { extend: 'excel', className: 'btn btn-sm btn-success' },
            { extend: 'csv', className: 'btn btn-sm btn-info' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger' },
            { extend: 'print', className: 'btn btn-sm btn-secondary' }
        ],
        ordering: true,
        paging: true,
        searching: true
    });
});
</script>


<?= $this->endSection() ?>
