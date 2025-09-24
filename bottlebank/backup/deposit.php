<?php
require_once 'db_connect.php';
require_once 'header.php';

function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

// POST: create or update deposit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_name = trim($_POST['user_name'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);

    if ($action === 'create') {
        $stmt = $pdo->prepare('INSERT INTO deposit (user_name, amount, deposit_date) VALUES (?, ?, NOW())');
        $stmt->execute([$user_name, $amount]);
        header('Location: deposit.php');
        exit;
    }

    if ($action === 'update') {
        $id = intval($_POST['deposit_id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE deposit SET user_name = ?, amount = ? WHERE deposit_id = ?');
        $stmt->execute([$user_name, $amount, $id]);

        // sync linked return rows that reference this deposit_id
        $stmt2 = $pdo->prepare('UPDATE `return` SET customer_name = ? WHERE deposit_id = ?');
        $stmt2->execute([$user_name, $id]);

        header('Location: deposit.php');
        exit;
    }
}

// GET: delete deposit
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // delete deposit; FK on return has ON DELETE CASCADE, but we also remove to be explicit
    $stmt = $pdo->prepare('DELETE FROM deposit WHERE deposit_id = ?');
    $stmt->execute([$id]);
    header('Location: deposit.php');
    exit;
}

// fetch single for edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare('SELECT deposit_id, user_name, amount FROM deposit WHERE deposit_id = ?');
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
}

// fetch all deposits
$stmt = $pdo->query('SELECT deposit_id, user_name, amount, deposit_date FROM deposit ORDER BY deposit_id DESC');
$rows = $stmt->fetchAll();
?>

<h2>Deposit</h2>

<form method="post" class="form-inline">
  <input type="hidden" name="action" value="<?php echo $edit ? 'update' : 'create'; ?>">
  <?php if ($edit): ?>
    <input type="hidden" name="deposit_id" value="<?php echo h($edit['deposit_id']); ?>">
  <?php endif; ?>

  <label>User name</label>
  <input type="text" name="user_name" required value="<?php echo $edit ? h($edit['user_name']) : ''; ?>">

  <label>Amount</label>
  <input type="number" name="amount" step="0.01" required value="<?php echo $edit ? h($edit['amount']) : ''; ?>">

  <button type="submit"><?php echo $edit ? 'Update' : 'Add'; ?></button>
  <?php if ($edit): ?><a href="deposit.php">Cancel</a><?php endif; ?>
</form>

<?php if ($rows): ?>
  <table class="table">
    <thead>
      <tr><th>ID</th><th>User</th><th>Amount</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?php echo h($r['deposit_id']); ?></td>
          <td><?php echo h($r['user_name']); ?></td>
          <td><?php echo number_format($r['amount'], 2); ?></td>
          <td><?php echo h($r['deposit_date']); ?></td>
          <td>
            <a href="deposit.php?edit=<?php echo h($r['deposit_id']); ?>">Edit</a>
            <a href="deposit.php?delete=<?php echo h($r['deposit_id']); ?>" onclick="return confirm('Delete this deposit? Deleting will also remove linked return rows.')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No deposit found</p>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
