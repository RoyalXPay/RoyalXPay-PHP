<?php $permissions = session()->get('permissions'); ?>
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Employees</a></li>
                        <li class="breadcrumb-item active">Employee List</li>
                    </ol>

                    <div class="page-title-right">
                        <?php if (!empty($userPermissions) && in_array('add', $userPermissions)) { ?>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#newEmployeeModal" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i>Add Employee
                            </button>
                        <?php } ?>
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
                                                <a href="<?= site_url('employees'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
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
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sl</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Permissions</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Created At</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $employee) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($employee->name); ?></td>
                                                <td><?= esc($employee->email); ?></td>
                                                <td>
                                                    <?php
                                                    $permissions = json_decode($employee->permissions, true);
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
                                                <td><?= esc($employee->status); ?></td>
                                                <td><?= esc(date('j M Y, h:i A', strtotime($employee->created_at))); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <?php if (!empty($userPermissions) && in_array('edit', $userPermissions)) { ?>
                                                            <a href="#" class="btn btn-success m-1 editBtn" data-id="<?= $employee->user_id ?>" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>

                                                        <a href="<?= site_url("employees/preview/" . $employee->user_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <?php if (!empty($userPermissions) && in_array('delete', $userPermissions)) { ?>
                                                            <form method="post" action="#" class="m-1">
                                                                <input type="hidden" name="employeeId" value="<?= $employee->user_id; ?>">
                                                                <button type="button" class="btn btn-danger dltBtn" data-id="<?= $employee->user_id; ?>" data-bs-toggle="tooltip" title="Delete">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        <?php } ?>
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
<?= $this->include('employees/create') ?>
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
        // Check if the search form should be shown
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000; // 10 minutes in milliseconds
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').show();
        } else {
            $('.search-form').hide();
        }

        $('#toggleSearchBtn').click(function() {
            const isVisible = $('.search-form').is(':visible');

            if (isVisible) {
                $('.search-form').hide();
                localStorage.setItem('isSearchFormVisible', 'false');
            } else {
                $('.search-form').show(); // Show the form
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        $('#newEmployeeModal').on('hidden.bs.modal', function() {
            $('#employeeForm')[0].reset();
            $('#modalMessages').html('');
            $('#employeeId').val('');
            $('#submitButton').text('Save');
            $('#newEmployeeModalLabel').text('Add Customer');
        });

        // Handle form submission
        $('#employeeForm').on('submit', function(e) {
            e.preventDefault();

            if (!validatePassword()) {
                return;
            }

            var url = "<?= site_url('employees/save') ?>";
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
                            $('#newEmployeeModal').modal('hide');
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
            var employeeId = $(this).data('id');

            // Fetch data via AJAX
            $.ajax({
                url: '<?= site_url('employees/edit') ?>/' + employeeId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate basic form fields
                    $('#employeeId').val(data.user_id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#employeeStatus').val(data.status);

                    // --- Handle Permissions ---
                    var selectedPermissions = Array.isArray(data.permissions) ? data.permissions : [];

                    // Uncheck all permission checkboxes
                    $('#permissionView, #permissionAdd, #permissionEdit, #permissionDelete').prop('checked', false);

                    // Check selected permissions
                    selectedPermissions.forEach(function(permission) {
                        $('#permission' + permission.charAt(0).toUpperCase() + permission.slice(1)).prop('checked', true);
                    });

                    // --- Handle Module Access ---
                    var selectedModules = [];

                    if (typeof data.module_access === 'string') {
                        try {
                            selectedModules = JSON.parse(data.module_access);
                        } catch (e) {
                            console.error('Failed to parse modules:', e);
                            selectedModules = [];
                        }
                    } else if (Array.isArray(data.module_access)) {
                        selectedModules = data.module_access;
                    }

                    // Uncheck all module checkboxes
                    $('#employeeForm input[name="modules[]"]').prop('checked', false);

                    // Check selected modules
                    selectedModules.forEach(function(module) {
                        $('#' + module).prop('checked', true);
                    });

                    // Update modal title and button text
                    $('#submitButton').text('Update');
                    $('#newEmployeeModalLabel').text('Edit Employee');

                    // Show the modal
                    $('#newEmployeeModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch employee data:', error);
                }
            });
        });

        $('.dltBtn').click(function(e) {
            e.preventDefault();
            var dataID = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('employees/delete') ?>",
                        data: {
                            employeeId: dataID
                        },
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
                } else {
                    swal("Your Data Is Safe!", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                }
            });
        });
    });

    function validatePassword() {
        let password = $('#password').val();
        let confirmPassword = $('#confirmPassword').val();
        let errorDiv = $('#passwordError');

        if (password !== confirmPassword) {
            errorDiv.show();
            return false;
        } else {
            errorDiv.hide();
            return true;
        }
    }

    function togglePassword(fieldId) {
        let field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }
</script>
<?= $this->endSection() ?>