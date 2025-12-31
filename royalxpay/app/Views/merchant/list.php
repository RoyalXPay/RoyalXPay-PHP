<h4>Merchant Privileges</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Module</th>
            <th>Submodule</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($privileges)): ?>
            <?php foreach ($privileges as $p): ?>
                <tr>
                    <td><?= esc($p['module']) ?></td>
                    <td><?= esc($p['submodule']) ?></td>
                    <td><?= esc($p['action']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No privileges assigned</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
