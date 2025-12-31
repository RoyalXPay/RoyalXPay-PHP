<table border="1">
  <tr><th>ID</th><th>User</th><th>Service</th><th>Amount</th><th>Time</th></tr>
  <?php foreach ($transactions as $t): ?>
  <tr>
    <td><?= $t['id'] ?></td>
    <td><?= $t['user'] ?></td>
    <td><?= $t['service_id'] ?></td>
    <td><?= $t['amount'] ?></td>
    <td><?= $t['created_at'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>
