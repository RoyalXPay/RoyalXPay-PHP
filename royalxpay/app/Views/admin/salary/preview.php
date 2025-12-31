<?php $permissions = session()->get('permissions'); ?>
<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        /* Initially hide the search form */
    }
</style>
<div class="page-content">
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-lg-12 mt-2">
            <div class="card employee-details-card">
               <div class="card-body p-4">
                  <!-- Existing Employee Details Header -->
                  <div class="detail-header d-flex justify-content-between align-items-center">
                     <h2 class="mb-0 font-weight-semibold text-primary">
                        <i class="fas fa-user-tie mr-2"></i>
                        Employee Details
                     </h2>
                     <a href="<?php echo site_url('employees') ?>" class="btn btn-outline-secondary back-btn">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                     </a>
                  </div>


                  <div class="row mt-4">
                     <div class="col-md-3 text-center mb-4">
                        <?php if (!empty($employee['profile_picture'])): ?>
                           <img src="<?php echo base_url('uploads/profile_pictures/' . $employee['profile_picture']); ?>" class="profile-picture mb-3" alt="Profile Picture">
                        <?php else: ?>
                           <div class="profile-picture mb-3 bg-light d-flex align-items-center justify-content-center">
                              <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                           </div>
                        <?php endif; ?>

                        <div class="status-badge <?php echo ($employee['status'] == 'active') ? 'status-active' : 'status-inactive'; ?>">
                           <?php echo ucfirst($employee['status']); ?>
                        </div>
                     </div>

                     <div class="col-md-9">
                        <div class="detail-container">
                           <!-- Example existing detail item -->
                           <div class="row detail-item align-items-center">
                              <div class="col-md-3 detail-label">Employee Code</div>
                              <div class="col-md-9 detail-value"><?php echo $employee['employee_code'] ?? 'N/A'; ?></div>
                           </div>

                           <div class="row detail-item align-items-center">
                              <div class="col-md-3 detail-label">Full Name</div>
                              <div class="col-md-9 detail-value"><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></div>
                           </div>

                           <div class="row detail-item align-items-center">
                              <div class="col-md-3 detail-label">Email</div>
                              <div class="col-md-9 detail-value">
                                 <a href="mailto:<?php echo $employee['email']; ?>">
                                    <?php echo $employee['email'] ?? 'N/A'; ?>
                                 </a>
                              </div>
                           </div>

                        </div>

                     </div>
                  </div>

                  <!-- NEW Salary Section -->
                  <div class="salary-section">
                     <h3 class="salary-header"><i class="fas fa-dollar-sign mr-2"></i>Employee Salary</h3>
                     <div class="detail-container">
                        <div class="row detail-item align-items-center">
                           <div class="col-md-3 detail-label">Current Salary</div>
                           <div class="col-md-9 detail-value">
                              <?php
                              if (!empty($salary['amount'])) {
                                 echo htmlspecialchars($salary['currency'] ?? '$') . number_format($salary['amount'], 2);
                              } else {
                                 echo 'N/A';
                              }
                              ?>
                           </div>
                        </div>

                        <div class="row detail-item align-items-center">
                           <div class="col-md-3 detail-label">Effective Date</div>
                           <div class="col-md-9 detail-value">
                              <?php
                              if (!empty($salary['effective_date'])) {
                                 echo date('F j, Y', strtotime($salary['effective_date']));
                              } else {
                                 echo 'N/A';
                              }
                              ?>
                           </div>
                        </div>

                        <div class="row detail-item align-items-center">
                           <div class="col-md-3 detail-label">Last Updated</div>
                           <div class="col-md-9 detail-value">
                              <?php
                              if (!empty($salary['updated_at'])) {
                                 echo date('F j, Y h:i A', strtotime($salary['updated_at']));
                              } else {
                                 echo 'Not updated yet';
                              }
                              ?>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Existing buttons -->
                  <div class="mt-4 d-flex justify-content-between">
                     <div>
                        <a href="#" class="btn btn-outline-primary">
                           <i class="fas fa-print mr-2"></i> Print Profile
                        </a>
                     </div>
                     <div>
                        <a href="<?php echo site_url('employees/edit/' . $employee['employee_id']); ?>" class="btn btn-primary mr-2">
                           <i class="fas fa-edit mr-2"></i> Edit Profile
                        </a>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?= $this->endSection() ?>