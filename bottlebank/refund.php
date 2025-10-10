<?php
include("db_connect.php");
include("header.php");

// Handle Add
if (isset($_POST['add'])) {
    $customer = trim($_POST['customer']);
    $amount = (float) $_POST['amount'];
    if ($customer != '' && $amount > 0) {
        $conn->query("INSERT INTO refund (customer, amount) VALUES ('$customer', $amount)");
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $customer = trim($_POST['customer']);
    $amount = (float) $_POST['amount'];
    if ($id > 0 && $customer != '' && $amount > 0) {
        $conn->query("UPDATE refund SET customer='$customer', amount=$amount WHERE id=$id");
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $conn->query("DELETE FROM refund WHERE id=$id");
    }
}

// Fetch All
$result = $conn->query("SELECT * FROM refund ORDER BY refund_date DESC, created_at DESC");
?>

<h2>💵 Refund Records</h2>

<form method="POST">
  <input type="text" name="customer" placeholder="Customer Name" required>
  <input type="number" step="0.01" name="amount" placeholder="Amount" required>
  <button type="submit" name="add">Add Refund</button>
</form>

<br>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Amount (PHP)</th>
    <th>Date</th>
    <th>Action</th>
  </tr>

  <?php
  if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".$row['id']."</td>";
          echo "<td>".$row['customer']."</td>";
          echo "<td>".number_format($row['amount'], 2)."</td>";
          echo "<td>".$row['refund_date']."</td>";
          echo "<td>
                <a href='?delete=".$row['id']."' onclick=\"return confirm('Delete this record?')\">Delete</a>
                <form method='POST' style='display:inline-block; margin-left:8px;'>
                    <input type='hidden' name='id' value='".$row['id']."'>
                    <input type='text' name='customer' value='".$row['customer']."' required>
                    <input type='number' step='0.01' name='amount' value='".$row['amount']."' required>
                    <button type='submit' name='update'>Update</button>
                </form>
                </td>";
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='5'>No refund records found</td></tr>";
  }
  ?>
</table>

<?php include("footer.php"); ?>
