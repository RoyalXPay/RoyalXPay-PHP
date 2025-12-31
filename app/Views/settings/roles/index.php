<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-form {
        display: none;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Role List</li>
                    </ol>

                    <div class="page-title-right">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#roleModal" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus"></i> Add New
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?= view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Sl</th>
                                            <th class="text-left">Merchant Name</th>
                                            <th class="text-left">Description</th>
                                            <th class="text-left">Permissions</th>
                                            <th class="text-left">Created At</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $role) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($role->role_name); ?></td>
                                                <td><?= esc($role->description); ?></td>
                                                <td>
                                                    <?php
                                                    $permissions = json_decode($role->role_permission, true);
                                                    if (!empty($permissions) && is_array($permissions)) {
                                                        foreach ($permissions as $perm) {
                                                            $badgeClass = 'badge-secondary';
                                                            switch ($perm) {
                                                                case 'view':
                                                                    $badgeClass = 'bg-primary';
                                                                    break;
                                                                case 'add':
                                                                    $badgeClass = 'bg-success';
                                                                    break;
                                                                case 'edit':
                                                                    $badgeClass = 'bg-warning text-dark';
                                                                    break;
                                                                case 'delete':
                                                                    $badgeClass = 'bg-danger';
                                                                    break;
                                                            }

                                                            echo '<span class="badge ' . $badgeClass . ' me-1">' . ucfirst($perm) . '</span>';
                                                        }
                                                    } else {
                                                        echo '<span class="text-muted">No Permissions</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!empty($role->created_at)) {
                                                        echo date('j M Y, h:i A', strtotime($role->created_at));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <button type="button" class="btn btn-success m-1 editBtn" data-id="<?= $role->role_id; ?>" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger m-1 dltBtn" data-id="<?= $role->role_id; ?>" data-bs-toggle="tooltip" title="Delete">
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
            </div>
        </div>
    </div>
</div>

<?= $this->include('settings/roles/create') ?>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        $('#rolePermission').selectpicker();
        $('[data-bs-toggle="tooltip"]').tooltip();

        $('#roleModal').on('hidden.bs.modal', function() {
            $('#roleForm')[0].reset();
            $('#modalMessages').html('');
            $('#roleId').val('');
            $('#submitButton').text('Save');
            $('#roleModalLabel').text('Add New Role');
        });

        // Handle form submission
        $('#roleForm').on('submit', function(e) {
            e.preventDefault();
            var url = "<?= site_url('roles/save') ?>";
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
                            $('#roleModal').modal('hide');
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
            var roleId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('roles/edit') ?>/' + roleId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#roleId').val(data.role_id);
                    $('#roleName').val(data.role_name);
                    $('#description').val(data.description);

                    // Reset the selectpicker correctly
                    $('#rolePermission').empty();
                    $('#rolePermission').selectpicker('destroy');
                    var availablePermissions = ['view', 'add', 'edit', 'delete'];
                    // Add options dynamically
                    availablePermissions.forEach(function(permission) {
                        $('#rolePermission').append($('<option>', {
                            value: permission,
                            text: permission.charAt(0).toUpperCase() + permission.slice(1)
                        }));
                    });

                    // Set selected values
                    $('#rolePermission').val(data.role_permission);

                    // Reinitialize selectpicker
                    $('#rolePermission').selectpicker();

                    $('#submitButton').text('Update');
                    $('#roleModalLabel').text('Edit Role');

                    $('#roleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch role data:', error);
                }
            });
        });

        // Handle delete button click
        $('.dltBtn').click(function(e) {
            e.preventDefault();
            var roleId = $(this).data('id');

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
                        url: "<?= site_url('roles/delete/') ?>" + roleId,
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