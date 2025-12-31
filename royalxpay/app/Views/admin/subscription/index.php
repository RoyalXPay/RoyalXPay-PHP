<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-form {
        display: none;
    }
</style>

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Subscriptions</a></li>
                        <li class="breadcrumb-item active">Subscription Package List</li>
                    </ol>

                    <div class="page-title-right">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#newSubscriptionPackageModal" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus"></i> Add New Subscription Package
                        </button>
                        <button id="toggleSearchBtn" type="button" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-magnify"></i> Search
                        </button>

                        <a class="btn btn-secondary waves-effect waves-light" onclick="window.history.back();">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="search-form">
            <form action="">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label for="txtsearch">Search</label>
                                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Type to search...">
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('subscription'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?= view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-left">Sl</th>
                                            <th data-sortable="true" class="text-left">Title</th>
                                            <th data-sortable="true" class="text-left">Description</th>
                                            <th data-sortable="true" class="text-left">Price</th>
                                            <th data-sortable="true" class="text-left">Duration</th>
                                            <th data-sortable="true" class="text-left">Status</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $subscription) {
                                            $durations = [
    '1 Month'   => '1 Month',
    '6 Months'  => '6 Months',
    '12 Months' => '12 Months'
]; ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($subscription->title); ?></td>
                                                <td><?= esc($subscription->description); ?></td>
                                                <td><?= esc($subscription->price); ?></td>
                                                <td><?= esc($durations[$subscription->duration] ?? $subscription->duration); ?></td>
                                                <td><?= esc($subscription->status); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $subscription->id; ?>" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $subscription->id; ?>" data-bs-toggle="tooltip" title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['totalRecords']) { ?>
                                <br>
                                <?= view('admin/_paging', array('paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray)); ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div> <!-- End Page-content -->

<!-- Add/Edit Modal for Subscription Package -->
<?= $this->include('admin/subscription/create') ?>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        $('#toggleSearchBtn').click(function() {
            const isVisible = $('.search-form').is(':visible');
            if (isVisible) {
                $('.search-form').hide();
                localStorage.setItem('isSearchFormVisible', 'false');
            } else {
                $('.search-form').show();
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        $('#subscriptionPackageForm').on('hidden.bs.modal', function() {
            $('#subscriptionForm')[0].reset();
            $('#modalMessages').html('');
            $('#subscriptionId').val('');
            $('#submitButton').text('Save');
            $('#newSubscriptionPackageModalLabel').text('Add Subscription Package');
        });

        // Handle form submission for Subscription Package
        $('#subscriptionPackageForm').on('submit', function(e) {
            e.preventDefault();
            var url = "<?= site_url('subscription/save') ?>";
            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('#modalMessages').html('');

                    if (response.status === 'error') {
                        var errorMessages = '<ul>';
                        if (response.errors) {
                            for (var field in response.errors) {
                                if (response.errors.hasOwnProperty(field)) {
                                    errorMessages += '<li>' + response.errors[field] + '</li>';
                                }
                            }
                        }
                        errorMessages += '</ul>';
                        $('#modalMessages').html(` 
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ${errorMessages}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    } else if (response.status === 'success') {
                        $('#modalMessages').html(` 
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                        setTimeout(function() {
                            $('#subscriptionPackageForm').modal('hide');
                            location.reload();
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    $('#modalMessages').html(` 
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            An unexpected error occurred. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                }
            });
        });

        $('.editBtn').on('click', function(e) {
            e.preventDefault();
            var subscriptionId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('subscription/edit') ?>/' + subscriptionId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Fill form fields with data
                    $('#subscriptionPackageId').val(data.id);
                    $('#title').val(data.title);
                    $('#description').val(data.description);
                    $('#price').val(data.price);
                    $('#duration').val(data.duration);
                    $('#subscriptionStatus').val(data.status);

                    $('#imagePreview').val('');

                    // Show the existing image if available
                    if (data.image) {
                        var imageUrl = '<?= base_url('public/uploads/packages/') ?>' + data.image;
                        $('#imagePreview').attr('src', imageUrl);
                        $('#imagePreviewWrapper').show();
                    } else {
                        $('#imagePreviewWrapper').hide();
                    }

                    $('#submitButton').text('Update');
                    $('#newSubscriptionPackageModalLabel').text('Edit Subscription Package');

                    $('#newSubscriptionPackageModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch Subscription data:', error);
                }
            });
        });

        // Handle delete button click
        $('.dltBtn').click(function(e) {
            e.preventDefault();
            var subscriptionId = $(this).data('id');

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "GET",
                        url: "<?= site_url('subscription/delete/') ?>" + subscriptionId,
                        success: function(response) {
                            if (response.status === 'success') {
                                swal(response.message, {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                swal("Error!", response.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Error!", "An unexpected error occurred. Please try again.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>