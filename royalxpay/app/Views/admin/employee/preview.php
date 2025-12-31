<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">

   <!-- Custom CSS -->
   <style>
      .avatar-xl {
         width: 80px;
         height: 80px;
         line-height: 80px;
         font-size: 2.5rem;
      }

      .table-borderless tbody tr th {
         font-weight: 500;
         color: #6c757d;
      }

      .card-header {
         padding: 1rem 1.25rem;
         background-color: #f8f9fa !important;
      }
   </style>
   <div class="container-fluid">
      <!-- Page Title & Breadcrumbs -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="<?= site_url('employees') ?>">Employee Management</a></li>
                  <li class="breadcrumb-item active"><?= esc($pageTitle) ?></li>
               </ol>

               <div class="page-title-right">
                  <a href="<?= site_url('employees') ?>" class="btn btn-secondary waves-effect waves-light">
                     <i class="mdi mdi-arrow-left"></i> Back to List
                  </a>
               </div>
            </div>
         </div>
      </div>
<?php 
$session = session();
$user_type = strtolower($session->get('user_type')); 

?>
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-body">
                  <!-- Employee Profile Header -->
                  <div class="row mb-4">
                     <div class="col-md-12">
                        <div class="d-flex align-items-center">
                           <div class="flex-shrink-0 me-3">
                              <?php if (!empty($employee['profile_picture'])):
                                 $imagePath = base_url('uploads/profile_pictures/' . $employee['profile_picture']);
                              ?>
                                 <img src="<?= $imagePath ?>"
                                    alt="Profile Image"
                                    class="avatar-xl rounded-circle">
                              <?php else: ?>
                                 <div class="avatar-xl rounded-circle bg-light text-center d-flex align-items-center justify-content-center">
                                    <span class="display-4 text-muted"><?= strtoupper(substr($employee['first_name'], 0, 1)) ?></span>
                                 </div>
                              <?php endif; ?>
                           </div>
                           <div class="flex-grow-1">
                              <h4 class="mb-1"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></h4>
                              <div class="d-flex flex-wrap gap-2">
                                 <span class="badge bg-<?= $employee['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(esc($employee['status'])) ?>
                                 </span>
                                 <span class="badge bg-info">
                                    <?= esc($employee['employee_code'] ?? 'N/A') ?>
                                 </span>
                                 <span class="badge bg-primary">
                                    <?= esc($employee['designation'] ?? 'N/A') ?>
                                 </span>
                              </div>
                           </div>
                           <div class="flex-shrink-0">
                              <?php if (in_array($user_type, ['maker', 'superadmin', 'merchant'])){ ?>
                              <!-- <a href="<?= site_url('employees/edit/' . $employee['employee_id']) ?>"
                                 class="btn btn-primary me-2">
                                 <i class="fas fa-edit me-1"></i> Edit
                              </a> -->
                              <?php } ?>

                              <?php if (in_array($user_type, ['maker', 'superadmin', 'merchant'])){?>
   <!-- Maker, Superadmin, Merchant can edit -->
   <a href="<?= site_url('employees/edit/' . $employee['employee_id']) ?>"
      class="btn btn-primary me-2">
      <i class="fas fa-edit me-1"></i> Edit
   </a>

<?php } ?> 
<?php if ($user_type == 'checker'): ?>
<?php if (!empty($employee['approved_by'])): ?>
      <!-- Already approved -->
      <button type="button" class="btn btn-success me-2" disabled>
         <i class="fas fa-check me-1"></i> Approved
      </button>
   <?php else: ?>
      <!-- Show Approve button only if not approved -->
      <form action="<?= site_url('employees/approve/' . $employee['employee_id']) ?>" method="post" style="display:inline;">
         <?= csrf_field() ?>
         <button type="submit" class="btn btn-success me-2" onclick="return confirm('Are you sure you want to approve this employee?');">
            <i class="fas fa-check me-1"></i> Approve
         </button>
      </form>
   <?php endif; ?>
<?php endif; ?>

                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Main Details Section -->
                  <div class="row">
                     <!-- Personal Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i> Personal Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Full Name</th>
                                          <td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Date of Birth</th>
                                          <td>
                                             <?= !empty($employee['dob']) ? date('F j, Y', strtotime($employee['dob'])) : 'N/A' ?>
                                             <?php if (!empty($employee['dob'])): ?>
                                                <small class="text-muted">(Age: <?= date_diff(date_create($employee['dob']), date_create('today'))->y ?> years)</small>
                                             <?php endif; ?>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th>Gender</th>
                                          <td><?= ucfirst(esc($employee['gender'] ?? 'N/A')) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Phone Number</th>
                                          <td><?= esc($employee['phone'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Email Address</th>
                                          <td><?= esc($employee['email'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Address</th>
                                          <td><?= esc($employee['address'] ?? 'N/A') ?></td>
                                       </tr>
                                        <tr>
                                          <th>access role</th>
                                          <td><?= esc($employee['access_role'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Maker / Checker</th>
                                          <td><?php
                                             if (!empty($employee['created_by_type'])) {
                                                   echo 'Added by ' . ucfirst($employee['created_by_type']);
                                             } else {
                                                   echo 'N/A';
                                             }
                                          ?></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Employment Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-briefcase me-2"></i> Employment Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Employee Code</th>
                                          <td><?= esc($employee['employee_code'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Designation</th>
                                          <td><?= esc($employee['designation'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Department</th>
                                          <td><?= esc($employee['department'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Joined Date</th>
                                          <td>
                                            <?= !empty($employee['joining_date']) ? date('F j, Y', strtotime($employee['joining_date'])) : 'N/A' ?>
                                                <?php if (!empty($employee['joining_date'])): ?>
                                                   <small class="text-muted">(<?= date_diff(date_create($employee['joining_date']), date_create('today'))->y ?> years with company)</small>
                                                <?php endif; ?>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th>Account Status</th>
                                          <td>
                                             <span class="badge bg-<?= strtolower($employee['status']) === 'active' ? 'success' : 'danger' ?>">
                                                <?= ucfirst(esc($employee['status'])) ?>
                                             </span>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- System Information Card -->
                     <div class="col-md-12 mt-4">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> System Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Member Since</th>
                                          <td><?= date('F j, Y, g:i a', strtotime($employee['created_at'])) ?></td>
                                       </tr>
                                     <tr>
                                          <th>Last Updated</th>
                                          <td>
                                             <button class="btn btn-info btn-sm" id="btnLastUpdated" data-id="<?= $employee['employee_id'] ?>">Last Updates</button>
                                          </td>
                                       </tr>
                                       <!-- <tr>
                                          <th>Created By</th>
                                          <td>
                                             <?php
                                             $loggedInUserId = session()->get('user_id');
                                             $loggedInUserName = session()->get('name');

                                             if (isset($employee['created_by']) && $employee['created_by'] == $loggedInUserId) {
                                                echo esc($loggedInUserName);
                                             } else {
                                                echo esc($loggedInUserName ?? 'System');
                                             }
                                             ?>
                                          </td>
                                       </tr> -->
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                   <div id="historyList" class="container-fluid mt-4" style="display:none;"></div>


                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
document.getElementById('btnLastUpdated').addEventListener('click', function() {
    const employeeId = this.getAttribute('data-id');
    const historyDiv = document.getElementById('historyList');

    // Show loader while fetching
    historyDiv.innerHTML = '<p class="text-center">Loading...</p>';
    historyDiv.style.display = 'block';

    fetch(`<?= site_url('employees/last-updated-history') ?>/${employeeId}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                historyDiv.innerHTML = '<p class="text-center">No records found</p>';
            } else {
                let html = `
                    <div class="card border mt-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Last Updates</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Updated By</th>
                                            <th>Date & Time</th>
                                            <th>Change Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                data.forEach((item, index) => {
                    // Parse change_details JSON
                    let changes = '';
                    try {
                        const details = JSON.parse(item.change_details);
                        changes = Object.entries(details).map(([key, val]) =>
                            `<strong>${key}:</strong> ${val}`
                        ).join('<br>');
                    } catch(e) {
                        changes = item.change_details;
                    }

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.action.toUpperCase()}</td>
                            <td>${item.name || 'System'}</td>
                            <td>${new Date(item.created_at).toLocaleString()}</td>
                            <td>${changes}</td>
                        </tr>`;
                });

                html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>`;

                historyDiv.innerHTML = html;
            }
        })
        .catch(err => {
            console.error(err);
            historyDiv.innerHTML = '<p class="text-danger text-center">Failed to load history.</p>';
        });
});

</script>

<?= $this->endSection() ?>