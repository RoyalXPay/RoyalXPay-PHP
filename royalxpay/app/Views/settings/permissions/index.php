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
                        <li class="breadcrumb-item active">Permission List</li>
                    </ol>

                    <div class="page-title-right">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#permissionModal" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus"></i> Add New Permission
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
                                            <th class="text-left">Role</th>
                                            <th class="text-left">Module</th>
                                            <th class="text-left">Submodule</th>
                                            <th class="text-left">Permission Type</th>
                                            <th class="text-left">Created At</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $permission) {
                                           
                                             ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($permission->role_name); ?></td>
                                                <td><?= esc($permission->module_name); ?></td>
                                                <td><?= esc($permission->submodule_name); ?></td>
                                                <td><?= esc($permission->permission_type); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($permission->created_at)) {
                                                        echo date('j M Y, h:i A', strtotime($permission->created_at));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <button type="button" class="btn btn-success m-1 editPermissionBtn" data-id="<?= $permission->id; ?>" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger m-1 deletePermissionBtn" data-id="<?= $permission->id; ?>" data-bs-toggle="tooltip" title="Delete">
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

<?= $this->include('settings/permissions/create') ?>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        $('#moduleSelect').selectpicker();
        $('#submoduleSelect').selectpicker();
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Clear Modal on Close
        $('#permissionModal').on('hidden.bs.modal', function() {
            $('#permissionForm')[0].reset();
            $('#modalMessages').html('');
            $('#permissionId').val('');
            $('#submitButton').text('Save');
            $('#permissionModalLabel').text('Add Permission');
        });

        // Handle form submission for adding/editing permissions
        $('#permissionForm').on('submit', function(e) {
            e.preventDefault();
            var url = "<?= site_url('permissions/save') ?>";
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
                            $('#permissionModal').modal('hide');
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

        // Handle edit button click for permissions
        $('.editPermissionBtn').on('click', function(e) {
            e.preventDefault();
            var permissionId = $(this).data('id');

            $.ajax({
                url: '<?= site_url('permissions/edit') ?>/' + permissionId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#permissionId').val(data.id);
                    $('#roleSelect').val(data.role_id);
                    $('#moduleSelect').val(data.module_id);
                    $('#submoduleSelect').val(data.submodule_id);
                    $('#permissionTypeSelect').val(data.permission_type);

                    $('#submitButton').text('Update');
                    $('#permissionModalLabel').text('Edit Permission');

                    $('#permissionModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch permission data:', error);
                }
            });
        });

        // Handle delete button click for permissions
        $('.deletePermissionBtn').click(function(e) {
            e.preventDefault();
            var permissionId = $(this).data('id');

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
                        url: "<?= site_url('permissions/delete/') ?>" + permissionId,
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

    function loadSubmodules() {
        var moduleIds = $('#moduleSelect').val(); // Get selected modules

        if (moduleIds.length > 0) {
            $.ajax({
                url: "<?= base_url('modules/getSubmodules'); ?>", // Adjust URL
                type: "GET",
                data: {
                    module_id: moduleIds
                }, // Send selected module IDs
                dataType: "json",
                success: function(response) {
                    $('#submoduleSelect').empty(); // Clear previous options

                    if (response.length > 0) {
                        $.each(response, function(index, submodule) {
                            $('#submoduleSelect').append('<option value="' + submodule.submodule_id + '">' + submodule.submodule_name + '</option>');
                        });
                    }

                    $('#submoduleSelect').selectpicker('refresh'); // Refresh Bootstrap Select
                },
                error: function() {
                    alert("Error loading submodules");
                }
            });
        } else {
            $('#submoduleSelect').empty().selectpicker('refresh'); // Just clear and refresh if no module selected
        }
    }
</script>

<?= $this->endSection() ?>