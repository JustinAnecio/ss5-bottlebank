<?php 
include("db_connect.php");
include("header.php"); 
 
?>

<h2>🔄 Return Records</h2>

<!-- Add Form -->
<form method="POST">
  <input type="text" name="customer_name" placeholder="Customer Name" required>
  <input type="number" name="quantity" placeholder="Quantity" required>
  <button type="submit" name="add">Add Return</button>
</form>

<?php
// CREATE
if (isset($_POST['add'])) {
    $customer = $_POST['customer_name'] ?? '';
    $qty      = $_POST['quantity'] ?? 0;

    if (!empty($customer) && $qty > 0) {
        $amount = $qty * 5; // same rate
        mysqli_query($conn, "INSERT INTO refund (customer_name, quantity, amount) 
                             VALUES ('$customer', '$qty', '$amount')");
        header("Location: refund.php");
        exit;
    }
}


// DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM returns WHERE return_id=$id");
    header("Location: return.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    if (isset($_POST['return_id'], $_POST['customer_name'], $_POST['quantity'])) {
        $id       = (int) $_POST['return_id'];
        $customer = $_POST['customer_name'];
        $qty      = (int) $_POST['quantity'];

        if ($id && !empty($customer) && $qty > 0) {
            $stmt = $conn->prepare("UPDATE returns SET customer_name=?, quantity=? WHERE return_id=?");
            $stmt->bind_param("sii", $customer, $qty, $id);
            $stmt->execute();
            $stmt->close();

            header("Location: return.php");
            exit;
        }
    }
}
?>

<!-- Display Table -->
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th>ID</th><th>Customer</th><th>Quantity</th><th>Date</th><th>Actions</th>
  </tr>
  <?php
  // 👉 FIX: gumamit ng created_at column (hindi return_date na wala sa DB)
  $result = $conn->query("SELECT * FROM returns ORDER BY created_at DESC");

  while ($row = $result->fetch_assoc()) {
      echo "<tr>
              <td>{$row['return_id']}</td>
              <td>{$row['customer_name']}</td>
              <td>{$row['quantity']}</td>
              <td>{$row['created_at']}</td>
              <td>
                <a href='return.php?delete={$row['return_id']}'>Delete</a>
                <form method='POST' style='display:inline'>
                  <input type='hidden' name='return_id' value='{$row['return_id']}'>
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
