<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-2">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                          
                            <a href="<?php echo site_url("employee-tasks"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <?php echo view('admin/_topmessage'); ?>

                        <form class="needs-validation" method="post" action="<?= site_url('employee-tasks/save'); ?>" novalidate>
                            <input type="hidden" name="task_id" value="<?= isset($task) ? $task['task_id'] : ''; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="fw-bold">Task Title <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Enter task title"
                                            value="<?= isset($task) ? $task['title'] : ''; ?>"
                                            required>
                                        <div class="invalid-feedback">Please provide a task title.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="employee_id" class="fw-bold">Assign To <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        <select class="form-control select2" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?= $employee['employee_id'] ?>"
                                                    <?= (isset($task) && $task['employee_id'] == $employee['employee_id']) ? 'selected' : '' ?>>
                                                    <?= $employee['first_name'] . ' ' . $employee['last_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select an employee.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="fw-bold">Priority <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="">Select Priority</option>
                                            <option value="high" <?= (isset($task) && $task['priority'] == 'high') ? 'selected' : '' ?>>High</option>
                                            <option value="medium" <?= (isset($task) && $task['priority'] == 'medium') ? 'selected' : '' ?>>Medium</option>
                                            <option value="low" <?= (isset($task) && $task['priority'] == 'low') ? 'selected' : '' ?>>Low</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a priority level.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="fw-bold">Due Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" class="form-control" id="due_date" name="due_date"
                                            value="<?= isset($task) ? $task['due_date'] : date('Y-m-d'); ?>"
                                            min="<?= date('Y-m-d') ?>" required>
                                        <div class="invalid-feedback">Please select a due date.</div>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($task)): ?>
                                <div class="mb-3">
                                    <label for="taskStatus" class="fw-bold">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                                        <select class="form-control" id="taskStatus" name="status" required>
                                            <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="description" class="fw-bold">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="4" placeholder="Enter task details" required><?= isset($task) ? $task['description'] : ''; ?></textarea>
                                <div class="invalid-feedback">Please provide a task description.</div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="fw-bold">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional comments or notes"><?= isset($task) ? $task['notes'] : ''; ?></textarea>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?php echo site_url("employee-tasks"); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-save me-2"></i> <?= isset($task) ? 'Update' : 'Create'; ?> Task
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation script
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<?= $this->endSection() ?>