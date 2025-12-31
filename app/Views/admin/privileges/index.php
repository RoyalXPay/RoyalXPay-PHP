<?= $this->extend('admin/layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="page-content">
    <div class="container-fluid">

        <!-- Assign Privileges Form -->
        <div class="row justify-content-center">
            <div class="col-xl-8 mt-4">
                <div class="card shadow-sm">
                    

                    <div class="card-body p-4">
                     
                          <div class="d-flex justify-content-end mb-3">
                                <a href="<?= site_url('admin/privileges/merchant'); ?>" class="btn btn-primary">
                                    Show Merchant Privileges
                                </a>
                            </div>

                        <h4 class="mb-3">Assign Privileges</h4>
                    
                        <form method="post" action="<?= site_url('admin/privileges/store'); ?>">
                            <div class="mb-3">
                                <label>Merchant</label>
                                <select name="merchant_id" class="form-control" required>
                                    <option value="">Select Merchant</option>
                                    <?php foreach($merchants as $m): ?>
                           <option value="<?= $m['user_id']; ?>">
    <?= esc($m['name']); ?> (<?= esc($m['email']); ?>, <?= esc($m['phone']); ?>)
</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Module</label>
                                <select id="modules" name="module_id[]" multiple class="form-control select2" required>
                                    <?php foreach($modules as $mod): ?>
                                        <option value="<?= $mod['id']; ?>"><?= esc($mod['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Submodule</label>
                                <select id="submodules" name="submodule_id[]" multiple class="form-control select2" disabled></select>
                            </div>

                            <div class="mb-3">
                                <label>Sub-Submodule</label>
                                <select id="subsubmodules" name="sub_submodule_id[]" multiple class="form-control select2" disabled></select>
                            </div>

                            <h5>Permissions</h5>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="can_add" value="1"> Add</div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="can_edit" value="1"> Edit</div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="can_delete" value="1"> Delete</div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="can_view" value="1"> View</div>

                            <button type="submit" class="btn btn-success mt-3">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privileges List -->
      <div class="row mt-5">
    <div class="col-12">

  

        <!-- Privileges List (hidden by default) -->
        <div id="privilegesList" style="display:none;">
            <h4 class="mb-3">Merchant Privileges List</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Merchant</th>
                        <th>Module</th>
                        <th>Submodule</th>
                        <th>Sub-Submodule</th>
                        <th>Permissions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($privileges as $p): ?>
                    <tr>
                        <td><?= esc($p['merchant_name']); ?></td>
                        <td><?= esc($p['module_name']); ?></td>
                        <td><?= esc($p['submodule_name']); ?></td>
                        <td><?= esc($p['sub_submodule_name']); ?></td>
                        <td>
                            <?= $p['can_add'] ? 'Add ' : ''; ?>
                            <?= $p['can_edit'] ? 'Edit ' : ''; ?>
                            <?= $p['can_delete'] ? 'Delete ' : ''; ?>
                            <?= $p['can_view'] ? 'View ' : ''; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>



    </div>
</div>
<script>
$(document).ready(function () {
    $('#togglePrivileges').on('click', function () {
        window.location.href = "<?= site_url('admin/privileges/view'); ?>";
    });
});
</script>
<script>
var allSubmodules = <?= json_encode($submodules); ?>;
var allSubSubmodules = <?= json_encode($sub_submodules); ?>;

$(document).ready(function() {
    $('#modules, #submodules, #subsubmodules').select2({
        closeOnSelect: false,
        placeholder: "Select",
        width: '100%'
    });

    function populateSubmodules() {
        var selectedModules = $('#modules').val() || [];
        selectedModules = selectedModules.map(Number);

        var filtered = allSubmodules.filter(function(s) {
            return selectedModules.includes(Number(s.module_id));
        });

        var $submodules = $('#submodules');
        $submodules.empty();
        filtered.forEach(function(s) {
            $submodules.append('<option value="'+s.id+'">'+s.name+'</option>');
        });

        $submodules.prop('disabled', filtered.length === 0).trigger('change');
        populateSubsubmodules();
    }

    function populateSubsubmodules() {
        var selectedSubmodules = $('#submodules').val() || [];
        selectedSubmodules = selectedSubmodules.map(Number);

        var filtered = allSubSubmodules.filter(function(s) {
            return selectedSubmodules.includes(Number(s.submodule_id));
        });

        var $subsubmodules = $('#subsubmodules');
        $subsubmodules.empty();
        filtered.forEach(function(s) {
            $subsubmodules.append('<option value="'+s.id+'">'+s.name+'</option>');
        });

        $subsubmodules.prop('disabled', filtered.length === 0).trigger('change');
    }

    $('#modules').on('change', populateSubmodules);
    $('#submodules').on('change', populateSubsubmodules);
});
</script>

<?= $this->endSection(); ?>
