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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Notifications</a></li>
                        <li class="breadcrumb-item active">Notification List</li>
                    </ol>

                    <div class="page-title-right">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#newNotificationModal" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus"></i> Add New Notification
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
                                    <div class="col-lg-4">
                                        <label for="txtsearch">Search</label>
                                        <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Search Notifications...">
                                    </div>
                                    <div class="col-lg-4" style="margin-top: 27px;">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="<?= site_url('notifications'); ?>" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh"></i> Clear
                                        </a>
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
                                            <th>SL</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Attachment</th>
                                            <th>Created On</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $notification) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($notification->title); ?></td>
                                                <td><?= esc($notification->description); ?></td>
                                                <td class="text-center">
                                                    <img src="<?= base_url('public/uploads/notifications/' . $notification->attachment); ?>" class="img-fluid" style="max-height: 80px; object-fit: cover;">
                                                </td>
                                                <td><?= esc($notification->created_at); ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $notification->id; ?>" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= site_url("notifications/preview/" . $notification->id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $notification->id; ?>" data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-info m-1 sendBtn" data-id="<?= $notification->id; ?>" data-bs-toggle="tooltip" title="Send Notification">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/notifications/create') ?>

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

        $('#newNotificationModal').on('hidden.bs.modal', function() {
            $('#notificationForm')[0].reset();
            $('#modalMessages').html('');
            $('#notificationId').val('');
            $('#submitButton').text('Save');
            $('#newNotificationModalLabel').text('Add Notification');
        });

        // Handle form submission for Notification
        $('#notificationForm').on('submit', function(e) {
            e.preventDefault();
            var url = "<?= site_url('notifications/save') ?>";
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
                            $('#newNotificationModal').modal('hide');
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

        // Handle edit button click
        $('.editBtn').on('click', function(e) {
            e.preventDefault();
            var notificationId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('notifications/edit') ?>/' + notificationId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#notificationId').val(data.id);
                    $('#title').val(data.title);
                    $('#description').val(data.description);

                    $('#submitButton').text('Update');
                    $('#newNotificationModalLabel').text('Edit Notification');

                    // Show existing attachment
                    const previewContainer = document.getElementById('attachmentPreviewContainer');
                    const attachment = document.getElementById('attachmentPreview');

                    // Clear previous preview
                    attachment.src = '';
                    previewContainer.style.display = 'none';

                    if (data.attachment && data.attachment.trim() !== '') {
                        $('#attachment').removeAttr('required');

                        // Set image source for existing attachment
                        attachment.src = '<?= base_url('public/uploads/notifications/') ?>' + data.attachment;
                        previewContainer.style.display = 'block';

                    } else {
                        $('#attachment').attr('required', true);
                        previewContainer.style.display = 'none';
                    }

                    $('#newNotificationModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch product data:', error);
                }
            });
        });

        // Handle delete button click
        $('.dltBtn').click(function(e) {
            e.preventDefault();
            var notificationId = $(this).data('id');

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
                        url: "<?= site_url('notifications/delete/') ?>" + notificationId,
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

    document.getElementById('attachment').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('attachmentPreviewContainer');
        const attachment = document.getElementById('attachmentPreview');

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                attachment.src = e.target.result;
                previewContainer.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
</script>

<?= $this->endSection() ?>