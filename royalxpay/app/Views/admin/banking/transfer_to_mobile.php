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
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Transaction To bank</a></li>
                    </ol>

                   
                </div>
            </div>
        </div>


        <form action="">
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
                                            <input class="form-control" name="txtsearch" type="text" value="<?php echo isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search by Name/ Mobile/ Email">
                                        </div>

                                        <div class="col-lg-4" style="margin-top: 27px;">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                Submit
                                            </button>
                                            <a href="<?= site_url('employees'); ?>" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh"></i> Clear
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

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                   
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>