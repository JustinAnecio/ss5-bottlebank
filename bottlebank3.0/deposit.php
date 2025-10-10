<?php include("db_connect.php"); ?>
<?php include("header.php"); ?>

<h2>💰 Deposit Records</h2>

<form method="POST">
  <input type="text" name="customer_name" placeholder="Customer Name" required>
  <input type="number" name="quantity" placeholder="Quantity" required>
  <button type="submit" name="add">Add Deposit</button>
</form>

<?php
if (isset($_POST['add'])) {
    $customer = $_POST['customer_name'];
    $qty = (int) $_POST['quantity'];
    if ($qty > 0) {
        $amount = $qty * 5;
        $conn->query("INSERT INTO deposit (customer_name, quantity, amount) VALUES ('$customer', $qty, $amount)");
        header("Location: deposit.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM deposit WHERE deposit_id=$id");
    header("Location: deposit.php");
    exit;
}
?>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th><th>Customer</th><th>Quantity</th><th>Date</th><th>Action</th>
  </tr>
  <?php
  $result = $conn->query("SELECT * FROM deposit ORDER BY created_at DESC");
  while ($row = $result->fetch_assoc()) {
      echo "<tr>
        <td>{$row['deposit_id']}</td>
        <td>{$row['customer_name']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['created_at']}</td>
        <td>
          <a href='deposit.php?delete={$row['deposit_id']}' onclick=\"return confirm('Delete this record?')\">Delete</a>
        </td>
      </tr>";
  }
  ?>
</table>

<?php include("footer.php"); ?>
