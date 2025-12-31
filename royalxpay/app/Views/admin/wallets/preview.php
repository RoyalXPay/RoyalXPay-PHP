<div class="page-content adj">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-12">
            <h2 class="mt-6 font-weight-semibold">Customer Details</h2>
            <table class="table table-striped table-bordered m-top20">
               <tbody>
                  
                  <tr>
                     <th scope="row">Name</th>
                     <td><?php echo $customers['full_name']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Email</th>
                     <td><?php echo $customers['email']; ?></td>
                  </tr>


                  <tr>
                     <th scope="row">Phone</th>
                     <td><?php echo $customers['phone']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">alternate mobile No</th>
                     <td><?php echo $customers['alt_mobile_no']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">longitude</th>
                     <td><?php echo $customers['longitude']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Location</th>
                     <td><?php echo $customers['location']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">User_Refer</th>
                     <td><?php echo $customers['user_refer']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Refer_by</th>
                     <td><?php echo $customers['refer_by']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Point</th>
                     <td><?php echo $customers['point']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Company Name</th>
                     <td><?php echo $customers['company_name']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Type</th>
                     <td><?php echo $customers['user_type']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">GST No.</th>
                     <td><?php echo $customers['gst']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Address</th>
                     <td><?php echo $customers['address']; ?></td>
                  </tr>


                  <tr>
                     <th scope="row">PAN no.</th>
                     <td><?php echo $customers['pancard']; ?></td>
                  </tr>

                  <tr>
                     <th scope="row">Created at</th>
                     <td><?php echo $customers['created_date']; ?></td>
                  </tr>

               </tbody>
            </table>
            <a href="<?php echo site_url('customers') ?>" class="btn btn-primary w-100" style="width:100%;">Back</a>
         </div>
      </div>
   </div>
</div>