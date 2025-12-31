<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">


    <div class="text-center mb-5">
        <h2 class="fw-bold"><?= $pagetitle ?? 'Settings' ?></h2>
        <p class="text-muted">Manage system modules, submodules, roles, and permissions from here</p>
    </div>

    <div class="row g-4">

        <!-- Manage Modules -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center h-100 border-0 rounded-3">
                <div class="card-body">
                    <i class="fa fa-cubes fa-2x text-primary mb-3"></i>
                    <h5 class="card-title">Modules</h5>
                    <p class="text-muted small">Create & update system modules</p>
                    <a href="<?= site_url('modules') ?>" class="btn btn-primary w-100">Manage</a>
                </div>
            </div>
        </div>

        <!-- Manage Submodules -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center h-100 border-0 rounded-3">
                <div class="card-body">
                    <i class="fa fa-cube fa-2x text-success mb-3"></i>
                    <h5 class="card-title">Submodules</h5>
                    <p class="text-muted small">Manage all submodules</p>
                    <a href="<?= site_url('submodules') ?>" class="btn btn-success w-100">Manage</a>
                </div>
            </div>
        </div>

         <!-- Manage Roles -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center h-100 border-0 rounded-3">
                <div class="card-body">
                    <i class="fa fa-users fa-2x text-danger mb-3"></i>
                    <h5 class="card-title">Roles</h5>
                    <p class="text-muted small">Define & assign roles</p>
                    <a href="<?= site_url('roles') ?>" class="btn btn-danger w-100">Manage</a>
                </div>
            </div>
        </div>

        <!-- Manage Permissions -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center h-100 border-0 rounded-3">
                <div class="card-body">
                    <i class="fa fa-lock fa-2x text-warning mb-3"></i>
                    <h5 class="card-title">Permissions</h5>
                    <p class="text-muted small">Set role-based access</p>
                    <a href="<?= site_url('permissions') ?>" class="btn btn-warning w-100 text-white">Manage</a>
                </div>
            </div>
        </div>

       

    </div>

</div>
</div>
</div>


<?= $this->endSection() ?>
