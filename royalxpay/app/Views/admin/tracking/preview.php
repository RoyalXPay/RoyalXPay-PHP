<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
   .btn-outline-secondary {
      opacity: 1 !important;
      border-color: #6c757d;
      color: #6c757d;
   }

   .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: white;
   }
</style>
<div class="page-content">
   <div class="container-fluid">
      <div class="row align-items-center mb-3">
         <div class="col-sm-12 d-flex justify-content-between align-items-center">
            <div class="page-title-box mb-0">
               <h4 class="font-size-18">Employee Locations</h4>
            </div>
            <a href="<?php echo site_url('employee-locations'); ?>" class="btn btn-outline-secondary">
               <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
         </div>
      </div>

      <form action="">
         <div class="row">
            <div class="col-xl-12">
               <div class="card ">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-lg-4">
                           <label>Search</label>
                           <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search by latitude / longitude">
                        </div>

                        <div class="col-md-2">
                           <label for="from_date" class="form-label">From Date</label>
                           <input type="date" class="form-control" name="from_date" value="<?= isset($searchArray['from_date']) ? esc($searchArray['from_date']) : '' ?>">
                        </div>

                        <div class="col-md-2">
                           <label for="to_date" class="form-label">To Date</label>
                           <input type="date" class="form-control" name="to_date" value="<?= isset($searchArray['to_date']) ? esc($searchArray['to_date']) : '' ?>">
                        </div>

                        <div class="col-lg-4" style="margin-top: 27px;">
                           <button type="submit" class="btn btn-primary mr-1">Search</button>
                           <a href="<?= site_url('employee-locations/view/' . $searchArray['employee_id']); ?>" class="btn btn-secondary">Clear</a>
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
               <?php echo view('admin/_topmessage'); ?>
               <div class="card-body">
                  <?php if ($pagination["totalRecords"] > 0) { ?>
                     <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive w-100">
                           <thead>
                              <tr>
                                 <th class="text-center">Sl</th>
                                 <th class="text-center">First Name</th>
                                 <th class="text-center">Last Name</th>
                                 <th class="text-center">Accuracy</th>
                                 <th class="text-center">Altitude</th>
                                 <th class="text-center">Speed</th>
                                 <th class="text-center">Heading</th>
                                 <th class="text-center">Latitude</th>
                                 <th class="text-center">Longitude</th>
                                 <th class="text-center">Online Status</th>
                                 <th class="text-center">Last Seen At</th>
                                 <th class="text-center">Last Location At</th>
                                 <th data-sortable="false">Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($results as $item) { ?>
                                 <tr id="employee-row-<?= $item->employee_id ?>">
                                    <td class="text-center"><?= ++$startLimit; ?></td>
                                    <td class="text-center"><?= esc($item->first_name); ?></td>
                                    <td class="text-center"><?= esc($item->last_name); ?></td>
                                    <td class="text-center"><?= esc($item->accuracy); ?></td>
                                    <td class="text-center"><?= esc($item->altitude); ?></td>
                                    <td class="text-center"><?= esc($item->speed); ?></td>
                                    <td class="text-center"><?= esc($item->heading); ?></td>
                                    <td class="text-center"><?= esc($item->latitude ?? 'N/A'); ?></td>
                                    <td class="text-center"><?= esc($item->longitude ?? 'N/A'); ?></td>
                                    <td class="text-center">
                                       <?php if ($item->online_status): ?>
                                          <span class="badge bg-success">Online</span>
                                       <?php else: ?>
                                          <span class="badge bg-secondary">Offline</span>
                                       <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                       <?= !empty($item->last_seen_at) ? date('d M Y, h:i A', strtotime($item->last_seen_at)) : 'N/A'; ?>
                                    </td>
                                    <td class="text-center">
                                       <?= !empty($item->location_time) ? date('d M Y, h:i A', strtotime($item->location_time)) : 'N/A'; ?>
                                    </td>

                                    <td class="text-center">
                                       <!-- View Button -->
                                       <button type="button" class="btn btn-primary m-1 view-location" data-lat="<?= $item->latitude ?>" data-lng="<?= $item->longitude ?>" data-name="<?= $item->first_name . ' ' . $item->last_name ?>" title="View Location">
                                          <i class="fas fa-map-marker-alt"></i>
                                       </button>

                                       <button type="button" class="btn btn-danger m-1 delete-tracking" data-id="<?= $item->employee_id ?>" data-name="<?= $item->first_name . ' ' . $item->last_name ?>" title="Delete Record">
                                          <i class="fas fa-trash"></i>
                                       </button>
                                    </td>
                                 </tr>
                              <?php } ?>
                           </tbody>
                        </table>
                     </div>
                     <?php if ($pagination['totalRecords']) { ?>
                        <br>
                        <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
                     <?php } ?>
                  <?php } else { ?>
                     <?= view('admin/_noresult'); ?>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Delete Location Confirmation Modal -->
<div class="modal fade" id="deleteLocationModal" tabindex="-1" role="dialog" aria-labelledby="deleteLocationModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteLocationModalLabel">Confirm Location Deletion</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="text-center">
               <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
               <h5>Are you sure you want to delete this location?</h5>
               <p>This action cannot be undone and all related tracking data will be removed.</p>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteLocation">Delete</button>
         </div>
      </div>
   </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="deleteSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-body text-center p-5">
            <div class="mb-4">
               <i class="fas fa-check-circle fa-5x text-success"></i>
            </div>
            <h4 id="successMessage">Location deleted successfully!</h4>
            <button type="button" class="btn btn-primary mt-3" data-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">Close</button>
         </div>
      </div>
   </div>
</div>

<!-- Data Safe Modal -->
<div class="modal fade" id="dataSafeModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-body text-center p-5">
            <div class="mb-4">
               <i class="fas fa-shield-alt fa-5x text-primary"></i>
            </div>
            <h4>Your data is safe!</h4>
            <p class="text-muted">The location record was not deleted.</p>
            <button type="button" class="btn btn-primary mt-3" data-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">Close</button>
         </div>
      </div>
   </div>
</div>

<!-- View Location Modal (Google Maps) -->
<div class="modal fade" id="viewLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Employee Location</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body p-0">
            <div id="locationMap" style="height: 400px; width: 100%;"></div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>

<script>
   $(document).ready(function() {

      $('#datatable').DataTable({
         dom: 'Bfrtip',
         searching: false,
         paging: false,
         info: false,
      });

      $('[data-bs-toggle="tooltip"]').tooltip();

      $('#toggleSearchBtn').click(function() {
         $('.search-form').toggleClass('show');
         localStorage.setItem('isSearchFormVisible', $('.search-form').hasClass('show'));
         localStorage.setItem('searchFormVisible', new Date().getTime());

         if ($('.search-form').hasClass('show')) {
            $(this).html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
         } else {
            $(this).html('<i class="mdi mdi-magnify me-1"></i> Advanced Search').removeClass('btn-primary').addClass('btn-outline-primary');
         }
      });

      const currentTime = new Date().getTime();
      const visibilityDuration = 10 * 60 * 1000;
      const isFormVisible = localStorage.getItem('isSearchFormVisible');
      const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

      if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
         $('.search-form').addClass('show');
         $('#toggleSearchBtn').html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
      }

      // Delete Location Variables
      let currentLocationId = null;
      let currentLocationName = null;

      // Google Maps Variables
      let map = null;
      let marker = null;

      // ========================
      // DELETE LOCATION FUNCTIONALITY
      // ========================

      // When delete button is clicked for location
      $('.delete-tracking').on('click', function() {
         currentLocationId = $(this).data('id');
         currentLocationName = $(this).data('name');

         $('#deleteLocationModal .modal-body h5').html(
            `Are you sure you want to delete the location of: <strong>${currentLocationName}</strong>?`
         );

         $('#deleteLocationModal').modal('show');
      });

      // Confirm delete location
      $('#confirmDeleteLocation').on('click', function() {
         $('#deleteLocationModal').modal('hide');
         $('#confirmDeleteLocation').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

         $.ajax({
            url: '<?= site_url("employee-locations/delete") ?>',
            type: 'POST',
            data: {
               location_id: currentLocationId,
            },
            success: function(response) {
               if (response.success) {
                  $('#location-row-' + currentLocationId).remove();
                  $('#successMessage').html(response.message);
                  $('#deleteSuccessModal').modal('show');
               } else {
                  $('#successMessage').html(response.message);
                  $('#deleteSuccessModal').modal('show');
               }
            },
            error: function() {
               $('#successMessage').html(
                  `An error occurred while deleting the location: ${currentLocationName}.`
               );
               $('#deleteSuccessModal').modal('show');
            },
            complete: function() {
               $('#confirmDeleteLocation').html('Delete').prop('disabled', false);
            }
         });
      });

      // Cancel delete - show data safe modal
      $('#deleteLocationModal .btn-secondary').on('click', function() {
         $('#dataSafeModal').modal('show');
      });

      // ========================
      // VIEW LOCATION FUNCTIONALITY
      // ========================

      // Load Google Maps API dynamically
      function loadGoogleMaps() {
         return new Promise((resolve, reject) => {
            if (typeof google === 'object' && typeof google.maps === 'object') {
               resolve();
            } else {
               const script = document.createElement('script');
               script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAfNAxmkEDXAr3-uxC86gjU5lfkrPcb6Ic&libraries=places`;
               script.onload = resolve;
               script.onerror = reject;
               document.head.appendChild(script);
            }
         });
      }

      // View Location Button Click
      $('.view-location').on('click', async function() {
         const lat = parseFloat($(this).data('lat'));
         const lng = parseFloat($(this).data('lng'));
         const name = $(this).data('name');

         if (isNaN(lat) || isNaN(lng)) {
            alert('No valid location data available for this employee');
            return;
         }

         // Show loading state
         $('#viewLocationModal .modal-title').html(`Loading map for ${name}...`);
         $('#viewLocationModal').modal('show');
         $('#locationMap').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Loading map...</p></div>');

         try {
            // Load Google Maps if not already loaded
            await loadGoogleMaps();

            // Initialize map
            $('#viewLocationModal .modal-title').html(`Location of ${name}`);

            const mapElement = document.getElementById('locationMap');
            const location = new google.maps.LatLng(lat, lng);

            if (map) {
               map.setCenter(location);
               marker.setPosition(location);
            } else {
               map = new google.maps.Map(mapElement, {
                  center: location,
                  zoom: 15,
                  mapTypeId: google.maps.MapTypeId.ROADMAP,
                  streetViewControl: true,
                  mapTypeControl: true
               });

               marker = new google.maps.Marker({
                  position: location,
                  map: map,
                  title: `${name}'s Location`,
                  animation: google.maps.Animation.DROP
               });

               // Add info window
               const infoWindow = new google.maps.InfoWindow({
                  content: `
                        <div style="padding: 10px;">
                            <h6 style="margin-bottom: 5px;">${name}</h6>
                            <p style="margin: 0;">Lat: ${lat.toFixed(6)}</p>
                            <p style="margin: 0;">Lng: ${lng.toFixed(6)}</p>
                        </div>
                    `
               });

               marker.addListener('click', () => {
                  infoWindow.open(map, marker);
               });

               // Open info window by default
               infoWindow.open(map, marker);
            }

         } catch (error) {
            console.error('Error loading map:', error);
            $('#locationMap').html(`
                <div class="alert alert-danger m-3">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Failed to load map. Please try again later.
                    ${error.message ? `<br><small>${error.message}</small>` : ''}
                </div>
            `);
         }
      });

      // Clean up when modal closes
      $('#viewLocationModal').on('hidden.bs.modal', function() {
         if (marker) marker.setMap(null);
         $('#locationMap').empty(); // Clear the map div
      });

      // ========================
      // PAGE RELOAD AFTER MODAL CLOSE
      // ========================
      $('[data-dismiss="modal"]').on('click', function() {
         setTimeout(function() {
            location.reload();
         }, 300);
      });
   });
</script>

<?= $this->endSection() ?>