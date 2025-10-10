<?php
require_once 'db_connect.php';
require_once 'header.php';

// handle create/update for return
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $type = $_POST['type'] ?? ''; // 'return' or 'refund'
  if ($type === 'return_create') {
    $customer = trim($_POST['customer_name']);
    $item = trim($_POST['item']);
    $qty = intval($_POST['quantity']);
    $stmt = $pdo->prepare('INSERT INTO `return` (customer_name, item, quantity, return_date) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$customer, $item, $qty]);
    header('Location: return_refund.php');
    exit;
  }
  if ($type === 'return_update') {
    $id = intval($_POST['return_id']);
    $customer = trim($_POST['customer_name']);
    $item = trim($_POST['item']);
    $qty = intval($_POST['quantity']);
    $stmt = $pdo->prepare('UPDATE `return` SET customer_name = ?, item = ?, quantity = ? WHERE return_id = ?');
    $stmt->execute([$customer, $item, $qty, $id]);
    header('Location: return_refund.php');
    exit;
  }
  if ($type === 'refund_create') {
    $customer = trim($_POST['customer_name']);
    $amount = floatval($_POST['amount']);
    $stmt = $pdo->prepare('INSERT INTO refund (customer_name, amount, refund_date) VALUES (?, ?, NOW())');
    $stmt->execute([$customer, $amount]);
    header('Location: return_refund.php');
    exit;
  }
  if ($type === 'refund_update') {
    $id = intval($_POST['refund_id']);
    $customer = trim($_POST['customer_name']);
    $amount = floatval($_POST['amount']);
    $stmt = $pdo->prepare('UPDATE refund SET customer_name = ?, amount = ? WHERE refund_id = ?');
    $stmt->execute([$customer, $amount, $id]);
    header('Location: return_refund.php');
    exit;
  }
}

// handle deletes via GET
if (isset($_GET['delete_return'])) {
  $id = intval($_GET['delete_return']);
  $stmt = $pdo->prepare('DELETE FROM `return` WHERE return_id = ?');
  $stmt->execute([$id]);
  header('Location: return_refund.php');
  exit;
}
if (isset($_GET['delete_refund'])) {
  $id = intval($_GET['delete_refund']);
  $stmt = $pdo->prepare('DELETE FROM refund WHERE refund_id = ?');
  $stmt->execute([$id]);
  header('Location: return_refund.php');
  exit;
}

// fetch single items for edit
$editReturn = null;
if (isset($_GET['edit_return'])) {
  $id = intval($_GET['edit_return']);
  $stmt = $pdo->prepare('SELECT return_id, customer_name, item, quantity FROM `return` WHERE return_id = ?');
  $stmt->execute([$id]);
  $editReturn = $stmt->fetch();
}
$editRefund = null;
if (isset($_GET['edit_refund'])) {
  $id = intval($_GET['edit_refund']);
  $stmt = $pdo->prepare('SELECT refund_id, customer_name, amount FROM refund WHERE refund_id = ?');
  $stmt->execute([$id]);
  $editRefund = $stmt->fetch();
}

// fetch lists
$returns = $pdo->query('SELECT return_id, customer_name, item, quantity, return_date FROM `return` ORDER BY return_date DESC')->fetchAll();
$refunds = $pdo->query('SELECT refund_id, customer_name, amount, refund_date FROM refund ORDER BY refund_date DESC')->fetchAll();

function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>

<h2>Return</h2>

<!-- Return form -->
<form method="post" class="form-inline">
  <?php if ($editReturn): ?>
    <input type="hidden" name="type" value="return_update">
    <input type="hidden" name="return_id" value="<?php echo h($editReturn['return_id']); ?>">
  <?php else: ?>
    <input type="hidden" name="type" value="return_create">
  <?php endif; ?>

  <label>Customer</label>
  <input type="text" name="customer_name" required value="<?php echo $editReturn ? h($editReturn['customer_name']) : ''; ?>">

  <label>Item</label>
  <input type="text" name="item" required value="<?php echo $editReturn ? h($editReturn['item']) : ''; ?>">

  <label>Quantity</label>
  <input type="number" name="quantity" required value="<?php echo $editReturn ? h($editReturn['quantity']) : '1'; ?>">

  <button type="submit"><?php echo $editReturn ? 'Update' : 'Add Return'; ?></button>
  <?php if ($editReturn): ?><a href="return_refund.php">Cancel</a><?php endif; ?>
</form>

<?php if ($returns): ?>
  <table class="table">
    <thead><tr><th>ID</th><th>Customer</th><th>Item</th><th>Qty</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($returns as $r): ?>
        <tr>
          <td><?php echo h($r['return_id']); ?></td>
          <td><?php echo h($r['customer_name']); ?></td>
          <td><?php echo h($r['item']); ?></td>
          <td><?php echo h($r['quantity']); ?></td>
          <td><?php echo h($r['return_date']); ?></td>
          <td>
            <a href="return_refund.php?edit_return=<?php echo h($r['return_id']); ?>">Edit</a>
            <a href="return_refund.php?delete_return=<?php echo h($r['return_id']); ?>" onclick="return confirm('Delete this return?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No return found</p>
<?php endif; ?>

<hr>

<h2>Refund</h2>

<!-- Refund form -->
<form method="post" class="form-inline">
  <?php if ($editRefund): ?>
    <input type="hidden" name="type" value="refund_update">
    <input type="hidden" name="refund_id" value="<?php echo h($editRefund['refund_id']); ?>">
  <?php else: ?>
    <input type="hidden" name="type" value="refund_create">
  <?php endif; ?>

  <label>Customer</label>
  <input type="text" name="customer_name" required value="<?php echo $editRefund ? h($editRefund['customer_name']) : ''; ?>">

  <label>Amount</label>
  <input type="number" name="amount" step="0.01" required value="<?php echo $editRefund ? h($editRefund['amount']) : '0.00'; ?>">

  <button type="submit"><?php echo $editRefund ? 'Update' : 'Add Refund'; ?></button>
  <?php if ($editRefund): ?><a href="return_refund.php">Cancel</a><?php endif; ?>
</form>

<?php if ($refunds): ?>
  <table class="table">
    <thead><tr><th>ID</th><th>Customer</th><th>Amount</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($refunds as $r): ?>
        <tr>
          <td><?php echo h($r['refund_id']); ?></td>
          <td><?php echo h($r['customer_name']); ?></td>
          <td><?php echo number_format($r['amount'],2); ?></td>
          <td><?php echo h($r['refund_date']); ?></td>
          <td>
            <a href="return_refund.php?edit_refund=<?php echo h($r['refund_id']); ?>">Edit</a>
            <a href="return_refund.php?delete_refund=<?php echo h($r['refund_id']); ?>" onclick="return confirm('Delete this refund?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No refund found</p>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
