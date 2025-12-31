
<div class="page-content adj">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <h2 class="mb-4"><?php echo $pagetitle;?></h2>
                            <form class="custom-validation" method='post' action="<?php echo site_url('BankDetails/save'); ?>"
                                enctype='multipart/form-data'>

                                <?php echo view('admin/_topmessage'); ?>
                                <div class="row  form-group">
                                    <div class="col-lg-5 ">
                                        <label>Bank Id</label>
                                       <input type="text" name="bank_id" value="<?php echo (isset($edit) && !empty($edit)) ? $edit['bank_id'] : ''; ?>" placeholder="Enter Bank ID" class="form-control form-control-lg" required="">

                                    </div>
                                    
                                     <div class="col-lg-5 ">
                                        <label>Bank Name</label>
                                       <input type="text" name="bank_name" value="<?php echo (isset($edit) && !empty($edit)) ? $edit['bank_name'] : ''; ?>" placeholder="Enter Bank Name" class="form-control form-control-lg" required="">

                                    </div>
                                </div>
                                
                                <div class="row  form-group">
                                    <div class="col-lg-5">  
                                    <input type="hidden" name="id"  value="<?php echo (isset($edit) && !empty($edit)) ? $edit['id'] : ''; ?>">                                     
                                        <button type="submit"
                                            class="btn btn-lg btn-block btn-primary waves-effect waves-light mr-1">
                                            Submit
                                        </button>
                                    </div>
                                    <div class="col-lg-5">
                                        <a href="<?php echo site_url("bankdetails");?>"
                                            class="btn btn-lg btn-block btn-secondary waves-effect">Back </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
