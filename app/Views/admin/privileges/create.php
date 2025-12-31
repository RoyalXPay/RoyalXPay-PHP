<?= $this->extend('admin/layouts/main'); ?>
<?= $this->section('content'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>


.select2-results__option input[type=checkbox] {
    margin-right: 8px;
}


.img-preview-container { border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; background-color: #f8f9fa; transition: all 0.3s ease; }
.img-preview-container:hover { border-color: #adb5bd; }
.img-preview { max-width: 100%; max-height: 200px; border-radius: 6px; object-fit: cover; display: block; margin: 0 auto 10px; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
.section-title { position: relative; padding-bottom: 10px; margin: 25px 0 20px; color: #495057; }
.section-title:after { content: ''; position: absolute; left: 0; bottom: 0; width: 50px; height: 3px; background: #4e73df; border-radius: 3px; }
.form-control:focus, .form-select:focus { border-color: #4e73df; box-shadow: 0 0 0 0.25rem rgba(78,115,223,.25); }
.btn-outline-secondary { border-color: #d1d3e2; } 
.btn-outline-secondary:hover { background-color: #f8f9fa; }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-8 mt-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <a href="<?= site_url("privileges"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <form method="post" action="<?= site_url('admin/privileges/store'); ?>">
                            <div class="mb-3">
                                <label>Merchant</label>
                                <select name="merchant_id" class="form-control" required>
                                    <option value="">Select Merchant</option>
                                    <?php foreach($merchants as $m): ?>
                                        <option value="<?= $m['user_id']; ?>"><?= esc($m['name']); ?></option>
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
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="can_download" value="1"> Download</div>

                            <button type="submit" class="btn btn-success mt-3">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS if needed (optional, for styles) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Then Multiselect -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script>
var allSubmodules = <?= json_encode($submodules); ?>;
var allSubSubmodules = <?= json_encode($sub_submodules); ?>;

// Initialize Select2 after the DOM is ready
$(document).ready(function() {

    $('#modules, #submodules, #subsubmodules').select2({
        closeOnSelect: false,
        placeholder: "Select",
        width: '100%'
    });

    // Populate Submodules
    function populateSubmodules() {
        var selectedModules = $('#modules').val() || [];
        selectedModules = selectedModules.map(Number); // convert to integer

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

    // Populate SubSubmodules
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

    // Event listeners
    $('#modules').on('change', populateSubmodules);
    $('#submodules').on('change', populateSubsubmodules);

});


</script>

<?= $this->endSection(); ?>
