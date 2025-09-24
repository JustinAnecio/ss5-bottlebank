<?php 
include("db_connect.php"); 
include("header.php"); 
?>

<h2>💵 Refund Records</h2>

<!-- Add Form -->
<form method="POST">
  <input type="text" name="customer_name" placeholder="Customer Name" required>
  <input type="number" name="amount" placeholder="Amount" required>
  <button type="submit" name="add">Add Refund</button>
</form>

<?php
// CREATE
if (isset($_POST['add'])) {
    $customer = $_POST['customer_name'] ?? '';
    $amount   = $_POST['amount'] ?? 0;

    if (!empty($customer) && $amount > 0) {
        $stmt = $conn->prepare("INSERT INTO refund (customer_name, amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $customer, $amount);
        $stmt->execute();
        header("Location: refund.php");
        exit;
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM refund WHERE refund_id=$id");
    header("Location: refund.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    if (isset($_POST['refund_id'], $_POST['customer_name'], $_POST['amount'])) {
        $id       = (int) $_POST['refund_id'];
        $customer = $_POST['customer_name'];
        $amount   = (float) $_POST['amount'];

        if ($id && !empty($customer) && $amount > 0) {
            $stmt = $conn->prepare("UPDATE refund SET customer_name=?, amount=? WHERE refund_id=?");
            $stmt->bind_param("sdi", $customer, $amount, $id);
            $stmt->execute();
            header("Location: refund.php");
            exit;
        }
    }
}
?>

<!-- Display Table -->
<table>
  <tr>
    <th>ID</th><th>Customer</th><th>Amount</th><th>Date</th><th>Actions</th>
  </tr>
  <?php
  $result = $conn->query("SELECT * FROM refund ORDER BY refund_date DESC");
  while ($row = $result->fetch_assoc());

  ?>
</table>

<?php include("footer.php"); ?>
