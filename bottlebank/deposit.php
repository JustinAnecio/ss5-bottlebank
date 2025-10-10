<?php 
include("db_connect.php");
include("header.php"); 
 
?>

<h2>💰 Deposit Records</h2>

<!-- Add Form -->
<form method="POST">
  <input type="text" name="customer_name" placeholder="Customer Name" required>
  <input type="number" name="quantity" placeholder="Quantity" required>
  <button type="submit" name="add">Add Deposit</button>
</form>

<?php
// CREATE
if (isset($_POST['add'])) {
    $customer = $_POST['customer_name'] ?? '';
    $qty      = $_POST['quantity'] ?? 0;

    if (!empty($customer) && $qty > 0) {
        $amount = $qty * 5; // compute amount per bottle
        mysqli_query($conn, "INSERT INTO deposit (customer_name, quantity, amount) 
                             VALUES ('$customer', '$qty', '$amount')");
        header("Location: deposit.php");
        exit;
    }
}

// UPDATE
if (isset($_POST['update'])) {
    if (isset($_POST['deposit_id'], $_POST['customer_name'], $_POST['quantity'])) {
        $id       = (int) $_POST['deposit_id'];
        $customer = $_POST['customer_name'];
        $qty      = (int) $_POST['quantity'];

        if ($id && !empty($customer) && $qty > 0) {
            $amount = $qty * 5; // recompute amount
            mysqli_query($conn, "UPDATE deposit 
                                 SET customer_name='$customer', quantity='$qty', amount='$amount' 
                                 WHERE deposit_id=$id");
            header("Location: deposit.php");
            exit;
        }
    }
}


// DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM deposit WHERE deposit_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: deposit.php");
        exit;
    }
}
?>

<!-- Display Table -->
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th>ID</th><th>Customer</th><th>Quantity</th><th>Date</th><th>Actions</th>
  </tr>
  <?php
  $result = mysqli_query($conn, "SELECT * FROM deposit ORDER BY created_at DESC");
  while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>
              <td>{$row['deposit_id']}</td>
              <td>{$row['customer_name']}</td>
              <td>{$row['quantity']}</td>
              <td>{$row['created_at']}</td>
              <td>
                <a href='deposit.php?delete={$row['deposit_id']}'>Delete</a>
                <form method='POST' style='display:inline'>
                  <input type='hidden' name='deposit_id' value='{$row['deposit_id']}'>
                  <input type='text' name='customer_name' value='{$row['customer_name']}' required>
                  <input type='number' name='quantity' value='{$row['quantity']}' required>
                  <button type='submit' name='update'>Update</button>
                </form>
              </td>
            </tr>";
  }
  ?>
</table>

<?php include("footer.php"); ?>
