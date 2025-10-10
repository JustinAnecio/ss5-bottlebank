<?php
include("db_connect.php");
include("header.php");

$stmt = $pdo->query("SELECT * FROM user ORDER BY created_at DESC");
$user = $stmt->fetchAll();
?>

<h2>User Records</h2>
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Created At</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($user as $u): ?>
      <tr>
        <td><?= $u['user_id']; ?></td>
        <td><?= htmlspecialchars($u['username']); ?></td>
        <td><?= htmlspecialchars($u['email']); ?></td>
        <td><?= $u['created_at']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include("footer.php"); ?>
