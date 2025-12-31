<div class="page-content">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="page-title-box">
                    <h4 class="font-size-18"><?php echo $pagetitle; ?></h4>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="float-right d-none d-md-block">
                    <div class="dropdown">
                        <a class="btn btn-primary waves-effect waves-light" href="<?php echo site_url('add-customers'); ?>">
                            <i class="ion ion-md-add-circle-outline"></i> Add Customers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="" id="customersearch">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card ">

                        <div class="card-body">
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="row">

                                        <div class="col-lg-2">
                                            <label> Start Date </label>
                                            <input class="form-control" name="startDate" type="date" value="<?php echo isset($startDate) ? $startDate : ''; ?>">
                                        </div>

                                        <div class="col-lg-2">
                                            <label for="endDate">End Date</label>
                                            <input class="form-control" name="endDate" id="endDate" type="date" value="<?php echo isset($endDate) ? $endDate : ''; ?>">
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="txtsearch">Search</label>
                                            <input class="form-control" name="txtsearch" type="text" value="<?php echo isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search by Name/Mobile/Email/Refer-ID">
                                        </div>


                                        <div class="col-lg-4" style="margin-top: 27px;">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                Submit
                                            </button>
                                            <a href="<?php echo site_url('customers'); ?>">
                                                <button type="button" class="btn btn-primary waves-effect waves-light mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Clear Search Filters">
                                                    <i class="mdi mdi-refresh"></i>Clear
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- csv Download -->
        <a class="btn btn-primary waves-effect waves-light" href="<?php echo site_url('customers-bulk-download'); ?>">Export</a>

        <?php if (in_array(strtolower(session()->get('user_type')), [ 'Merchant']))if ($admin_type == 'Merchant') { ?>
            <a href="#" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#excelUploadModal">Import</a>
        <?php } ?>

        <!--end  _searchform -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <?php echo view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php // if ($pagination["getNbResults"] > 0) { ?>
                            <div class="table-responsive">
                                <table data-toggle="table" data-striped="true" class="table table-hover table-centered table-nowrap mb-0">
                                    <thead class="table table-bordered">
                                        <tr>
                                            <!-- checkbox -->
                                            <th>
                                                <button id="delete-all-btn" type="submit" style="font-size:16px;" class="btn btn-primary waves-effect waves-light m-2">Delete All</button>
                                            </th>
                                            <th class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Customers Name</th>
                                        
                                            <th data-sortable="true" class="text-center">Own Referral ID</th>
                                            <th data-sortable="true" class="text-center">Mobile</th>
                                            <th data-sortable="true" class="text-center">Email</th>
                                            <th data-sortable="true" class="text-center">State</th>
                                            <th data-sortable="true" class="text-center">District</th>
                                            <th data-sortable="true" class="text-center">Joined On</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table table-bordered">
                                        <?php foreach ($results as $value) { ?>

                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="selected[]" id="checkbox-<?php echo $value->user_id; ?>" class="regular-checkbox name" value="<?php echo $value->user_id; ?>" />
                                                    <label for="checkbox-<?php echo $value->user_id; ?>"></label>
                                                </td>
                                                <td class="text-center" scope="row"><?php echo ++$startLimit; ?></td>
                                                <td class="text-center"><?php echo $value->name; ?></td>
                                                <td class="text-center"><?php echo $value->user_refer; ?></td>
                                                <td class="text-center"><?php echo $value->phone; ?></td>
                                                <td class="text-center"><?php echo $value->email; ?></td>
                                                <td class="text-center"><?php echo $value->state_name; ?></td>
                                                <td class="text-center"><?php echo $value->city_name; ?></td>
                                                <td class="text-center"><?php echo $value->created_at; ?></td>
                                                <td class="text-center" width="8%">
                                                    <a href="#" 
   class="btn btn-warning" 
   data-toggle="modal" 
   data-target="#privilegeModal<?php echo $value->user_id; ?>">
   <i class="fas fa-user-shield"></i>
</a>
                                                    <a href="<?php echo site_url('show-customers?id=' . $value->user_id); ?>" class="btn btn-success" title="preview"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;&nbsp;
                                                    <?php if (in_array(strtolower(session()->get('user_type')), [ 'Merchant'])) { ?>
                                                    <a href="<?php echo site_url('edit-customers?id=' . $value->user_id); ?>" class="btn btn-primary" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;&nbsp;
                                                    <!-- Modal for Delete -->
                                                    <a data-toggle="modal" class="btn btn-danger" href="#myModal<?php echo $value->user_id; ?>"><i class="fa fa-trash"></i></a>
                                                    <?php } ?>
                                                    <!--  start modal --->

                                                    <div class="modal fade" id="myModal<?php echo $value->user_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">

                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="myModalLabel1">Delete Data</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                </div>
                                                                <!-- Modal Body -->
                                                                <div class="modal-body">
                                                                    <p>Are you sure you want to delete the Customer?</p>
                                                                </div>

                                                                <!-- Modal Footer -->
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                    <a href="<?php echo site_url('delete-customers?id=' . $value->user_id); ?>" class="btn btn-primary">OK</a>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!---- End modal --->
                                                </td>
                                            </tr>

                                        <?php } ?>
                                    </tbody>
                                </table>
                                <!-- <?php //if ($pagination['haveToPaginate']) { ?>
                                    <br>
                                    <?php //echo view('admin/_paging', array('paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray)); ?>
                                <?php //} ?> -->
                            </div>
                            <?php // } else { ?>
                            <?php// echo view('admin/_noresult'); ?>
                        <?php //} ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Excel File Upload -->
        <div class="modal fade" id="excelUploadModal" tabindex="-1" role="dialog" aria-labelledby="excelUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="excelUploadModalLabel">Upload Excel File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Your file upload form goes here -->
                        <form id="excelUploadForm" action="<?php echo site_url('customers-bulk-upload'); ?>" method="POST" enctype="multipart/form-data">
                            <input type="file" name="excelFile" id="excelFile">
                            <span class="text-danger" id="uploadFile"></span>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submitExcel">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="privilegeModal<?php echo $value->user_id; ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form action="<?= site_url('merchant/save-privileges') ?>" method="POST">
        <input type="hidden" name="merchant_id" value="<?= $value->user_id ?>">

        <div class="modal-header">
          <h5 class="modal-title">Manage Privileges for <?= $value->name ?></h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Feature</th>
                <th>Add</th>
                <th>Edit</th>
                <th>Delete</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $features = ['EMS', 'Saving Account', 'Money Transfer', 'AI Agent', 'Reports'];
              foreach ($features as $feature): ?>
                <tr>
                  <td><?= $feature ?></td>
                  <td><input type="checkbox" name="privileges[<?= $feature ?>][add]" value="1"></td>
                  <td><input type="checkbox" name="privileges[<?= $feature ?>][edit]" value="1"></td>
                  <td><input type="checkbox" name="privileges[<?= $feature ?>][delete]" value="1"></td>
                  <td><input type="checkbox" name="privileges[<?= $feature ?>][view]" value="1"></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Privileges</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>

    </div>
  </div>
</div>


<script>
    $(document).ready(function() {
        $('#delete-all-btn').click(function() {
            var selectedItems = [];
            $('input[name="selected[]"]:checked').each(function() {
                selectedItems.push($(this).val());
            });

            if (selectedItems.length > 0) {
                if (confirm('Are you sure you want to delete the selected items?')) {
                    $.ajax({
                        url: "<?php echo site_url('delete-all-customers'); ?>",
                        type: 'POST',
                        data: {
                            selected: selectedItems
                        },
                        success: function(response) {
                            // Handle the response from the server
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred while deleting the items');
                            console.log(error);
                        }
                    });
                }
            } else {
                alert('Please select at least one item to delete');
            }
        });
    });
</script>

<script type="text/javascript">
    $('#group-checkable').click(function() {
        var checkboxes = $(this).closest('form').find(':checkbox');
        if ($(this).is(':checked')) {
            checkboxes.attr('checked', 'checked');
        } else {
            checkboxes.removeAttr('checked');
        }
    });
</script>
<script>
    document.getElementById("submitExcel").addEventListener("click", function () {
        // Get the form element and the file input
        var form = document.getElementById("excelUploadForm");
        var fileInput = document.getElementById("excelFile");

        // Check if a file has been selected
        if (fileInput.files.length === 0) {
            document.getElementById("uploadFile").innerHTML = "Please select a file";
        } else {
            // Submit the form
            form.submit();
        }
    });
</script>