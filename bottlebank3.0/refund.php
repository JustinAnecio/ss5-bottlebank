<?php include("db_connect.php"); ?>
<?php include("header.php"); ?>

<h2>💵 Refund Records</h2>

<form method="POST">
  <input type="text" name="customer" placeholder="Customer Name" required>
  <input type="number" step="0.01" name="amount" placeholder="Amount" required>
  <button type="submit" name="add">Add Refund</button>
</form>

<?php
if (isset($_POST['add'])) {
    $customer = $_POST['customer'];
    $amount = (float) $_POST['amount'];
    if ($amount > 0) {
        $conn->query("INSERT INTO refund (customer, amount) VALUES ('$customer', $amount)");
        header("Location: refund.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM refund WHERE id=$id");
    header("Location: refund.php");
    exit;
}
?>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th><th>Customer</th><th>Amount</th><th>Date</th><th>Action</th>
  </tr>
  <?php
  $result = $conn->query("SELECT * FROM refund ORDER BY refund_date DESC");
  while ($row = $result->fetch_assoc()) {
      echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['customer']}</td>
        <td>".number_format($row['amount'],2)."</td>
        <td>{$row['refund_date']}</td>
        <td><a href='refund.php?delete={$row['id']}' onclick=\"return confirm('Delete this record?')\">Delete</a></td>
      </tr>";
  }
  ?>
</table>

<?php include("footer.php"); ?>
