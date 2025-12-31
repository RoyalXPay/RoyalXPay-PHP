<div class="page-content adj">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <h2 class="mb-4"><?php echo $pagetitle; ?></h2>
                            <form class="custom-validation" method='post' action="<?php echo site_url('update-customers'); ?>" enctype='multipart/form-data'>

                                <?php echo view('admin/_topmessage'); ?>

                                <input type="hidden" name="customer_id" value="<?php echo isset($edit['id']) ? $edit['id'] : ''; ?>">

                                <div class="row  form-group">
                                    <div class="col-lg-5 ">
                                        <label>Name</label>
                                        <input type="text" name="full_name" placeholder="Enter Full Name" value="<?php echo isset($edit['full_name']) ? $edit['full_name'] : ''; ?>" class="form-control form-control-lg" required>
                                    </div>


                                    <div class="col-lg-5">
                                        <label>Point</label>
                                        <input type="text" name="point" placeholder="Enter point" value="<?php echo isset($edit['point']) ? $edit['point'] : ''; ?>" class="form-control form-control-lg">
                                    </div>

                                </div>

                                <div class="row  form-group">
                                    <div class="col-lg-5 ">
                                        <label>Mobile No.</label>
                                        <input type="tel" pattern="[0-9]+" minlength="10" maxlength="10" name="phone" placeholder="Enter Mobile Number" value="<?php echo isset($edit['phone']) ? $edit['phone'] : ''; ?>" class="form-control form-control-lg" required>

                                    </div>

                                    <div class="col-lg-5 ">
                                        <label>Email</label>
                                        <input type="email" name="email" placeholder="Enter Email" value="<?php echo isset($edit['email']) ? $edit['email'] : ''; ?>" class="form-control form-control-lg" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="row  form-group">
                                    <div class="col-lg-5 ">
                                        <label>Password</label>
                                        <input type="password" name="password" value="<?php echo isset($edit['password']) ? $edit['password'] : ''; ?>" placeholder="Enter Password" class="form-control form-control-lg" autocomplete="off" disabled>
                                    </div>

                                    <div class="col-lg-5">
                                        <label>Gender</label>
                                        <select name="gender" class="select2 form-control form-control-lg" id="gender">
                                            <option disabled>-- Select Gender--</option>
                                            <option value="male" <?php echo (isset($edit['gender']) && $edit['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo (isset($edit['gender']) && $edit['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="transgender" <?php echo (isset($edit['gender']) && $edit['gender'] === 'transgender') ? 'selected' : ''; ?>>Transgender</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="row  form-group">
                                    <div class="col-lg-5 ">
                                        <label>GST Number</label>
                                        <input type="text" name="gst" value="<?php echo isset($edit['gst']) ? $edit['gst'] : ''; ?>" placeholder="Enter GST Number" class="form-control form-control-lg">
                                    </div>

                                    <div class="col-lg-5 ">
                                        <label>Company Name</label>
                                        <input type="text" name="company_name" value="<?php echo isset($edit['company_name']) ? $edit['company_name'] : ''; ?>" placeholder="Enter Company Name" class="form-control form-control-lg">

                                    </div>
                                </div>

                                <div class="row  form-group">
                                    <div class="col-lg-5 ">
                                        <label>State</label>
                                        <select name="state_id" class="form-control form-control-lg" id="state_id">
                                            <option value="">-- Select State--</option>
                                            <?php foreach ($states as $state) {
                                                $selected = "";
                                                if ($state['state_id'] == $edit['state_id']) {
                                                    $selected = "selected='selected'";
                                                }
                                            ?>
                                                <option <?php echo $selected; ?> value="<?php echo $state['state_id']; ?>">
                                                    <?php echo $state['state_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                   
                                    <div class="col-lg-5">
                                        <label>City</label>
                                        <select name="city_id" class="form-control form-control-lg" id="city_id">
                                            <option selected disabled>-- Select City--</option>

                                            <?php foreach ($cities as $city) {
                                                $selected = ($city['city_id'] == $edit['city_id']) ? "selected='selected'" : "";
                                            ?>
                                                <option value="<?php echo $city['city_id']; ?>" <?php echo $selected; ?>>
                                                    <?php echo $city['city_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row  form-group">

                                    <div class="col-lg-5 ">
                                        <label>Address</label>
                                        <textarea name="address" rows="2" class="form-control form-control-l"><?php echo isset($edit['address']) ? $edit['address'] : ''; ?></textarea>
                                    </div>

                                    <div class="col-lg-5 ">
                                        <label>Pincode</label>
                                        <input type="text" name="pincode" placeholder="Enter Pincode" value="<?php echo isset($edit['pincode']) ? $edit['pincode'] : ''; ?>" class="form-control form-control-lg">
                                    </div>

                                </div>

                                <div class="row  form-group">

                                    <div class="col-lg-5">
                                        <label>PAN Number</label>
                                        <input type="text" name="pancard" placeholder="Enter PAN Number" value="<?php echo isset($edit['pancard']) ? $edit['pancard'] : ''; ?>" class="form-control form-control-lg">
                                    </div>
                                    <div class="col-lg-5">
                                        <label>Refer By</label>
                                        <?php if ($admin_type == 'admin') { ?>
                                            <input type="text" name="refer_by" placeholder="Enter Reffer code" value="<?php echo isset($edit['refer_by']) ? $edit['refer_by'] : ''; ?>" class="form-control form-control-lg">
                                        <?php } else { ?>
                                            <input type="text" name="refer_by" placeholder="Enter Reffer code" value="<?php echo isset($edit['refer_by']) ? $edit['refer_by'] : ''; ?>" disabled class="form-control form-control-lg">
                                        <?php } ?>
                                    </div>

                                </div>

                                <div class="row  form-group">
                                    <div class="col-lg-5">
                                        <button type="submit" class="btn btn-lg btn-block btn-primary waves-effect waves-light mr-1">Submit</button>
                                    </div>

                                    <div class="col-lg-5">
                                        <a href="<?php echo site_url("customers"); ?>" class="btn btn-lg btn-block btn-secondary waves-effect"> Back </a>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div> <!-- container-fluid -->
<script>
    $(document).ready(function() {
        $('#state_id').change(function() {
            var stateId = $(this).val();
            $.ajax({
                url: '<?php echo site_url('get-cities'); ?>',
                method: 'POST',
                data: {
                    state_id: stateId
                },
                dataType: 'json',
                success: function(response) {

                    $('#city_id').empty();
                    // Add the default option
                    $('#city_id').append($('<option>', {
                        selected: 'selected',
                        disabled: 'disabled',
                        text: '-- Select City--'
                    }));

                    var cityOptions = JSON.parse(response);
                    $.each(cityOptions, function(key, value) {
                        $('#city_id').append($('<option>', {
                            value: key,
                            text: value
                        }));
                    });
                },
            });
        });
    });
</script>