<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.css">
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
                            <a class="btn btn-primary waves-effect waves-light" href="<?php echo site_url('addbank'); ?>">
                                <i class="ion ion-md-add-circle-outline"></i> Add New Bank
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

                                  Start Date <input class="form-control" name="startDate" type="date" value="<?php echo $startDate; ?>" placeholder="yyyy-mm-dd">

                                    
                                    </div>

                                    <div class="col-lg-2">
                                        End Date
                                   <input class="form-control" name="endDate" type="date" value="<?php echo $endDate; ?>" placeholder="yyyy-mm-dd">
                                    

                                    
                                    </div>

                                     <div class="col-lg-4" style="margin-top: 20px;">
                                    <input class="form-control" name="txtsearch" type="text" value="<?php echo $txtsearch; ?>" placeholder="Search by Name" >
                                    
                                    </div>
                                  
                                  <div class="col-lg-4" style="margin-top: 20px;">
                                     <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                        Submit
                                    </button>
                                    <a href="<?php echo site_url('bankdetails');?>"><button type="button" class="btn btn-primary waves-effect waves-light mr-1"
                                     data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Clear Search Filters">
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
            <!-- _searchform -->

 <!-- <form action="" id="customersearch">
<div class="row">
    <div class="col-xl-12">
            <div class="card ">
                
                    <div class="card-body">

                        <div class="row ">

                            <div class="col-lg-3 ">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control" name="txtsearch" type="text" value="<?php echo $txtsearch; ?>" placeholder="Search " >
                                    </div>
                                </div>
                            </div>
                           
    
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                        Submit
                                    </button>
                                    <a href="<?php echo site_url('SelesPartner');?>"><button type="button" class="btn btn-primary waves-effect waves-light mr-1"
                                     data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Clear Search Filters">
                                    <i class="mdi mdi-refresh"></i>Clear
                                    </button>
                                    </a>
                                

                                    
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 ">
                                <div class="row">
                                    <div class="col-md-12">
                                         <a href="<?php echo site_url('');?>"><button type="button" class="btn btn-primary waves-effect waves-light mr-1"
                                     data-toggle="tooltip" data-placement="top" >
                                    <i class="mdi mdi-export"></i>Export
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
</for -->

<!-- <script>
function exportdata()
          {
            var formdata = $('#customersearch').serialize();
            window.open("<?php echo site_url('customerexportexcel'); ?>?"+formdata);   
          }
    </script> -->

         <!--end  _searchform -->  

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <?php echo view('admin/_topmessage'); ?>
                        <div class="card-body">
                        
                        <?php if($pagination["getNbResults"] >0 ){ ?>
                            <div class="table-responsive">
                                 <table  data-toggle="table" data-striped="true" class="table table-hover table-centered table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">S No.</th>
                                            
                                            <th data-sortable="true" class="text-center">bank ID</th>
                                            <th data-sortable="true" class="text-center">Bank Name</th>
                                            <th data-sortable="true" class="text-center">craeted</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                       
                                        foreach($bank_data as $value){ ?>
                                        <tr>
                                            <td scope="row"><?php echo ++$startLimit ; ?></td>
                                            
                                            <td><?php echo $value->bank_id; ?></td>
                                            <td><?php echo $value->bank_name; ?></td>
                                            <td><?php echo $value->created; ?></td>

                                            <td width="8%">
                                                  <a href="<?php echo site_url('BankDetails/addbank/'.$value->id);?>" class="btn btn-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;&nbsp;
                                                  <a href="<?php echo site_url('BankDetails/delete/'.$value->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" title="delete"><i class="fa fa-trash"></i></a>
                                                   
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php if ($pagination['haveToPaginate']) { ?>
                                <br>
                                <?php echo view('admin/_paging', array('paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray)); ?>

                                <?php } ?>
                            </div>
                        <?php }else{ ?>
                            <?php echo view('admin/_noresult'); ?>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div> <!-- container-fluid -->

    </div>
    <!-- End Page-content -->  

    <script src="https://unpkg.com/bootstrap-table@1.20.2/dist/bootstrap-table.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>