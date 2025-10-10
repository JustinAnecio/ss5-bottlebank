<?php include("db_connect.php"); ?>
<?php include("header.php"); ?>

<h2>📊 System Report</h2>

<?php
$deposit = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM deposit")->fetch_assoc()['total'];
$return = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM returns")->fetch_assoc()['total'];
$refund = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM refund")->fetch_assoc()['total'];
?>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>Total Deposits</th>
    <th>Total Returns</th>
    <th>Total Refunds (₱)</th>
  </tr>
  <tr>
    <td><?php echo $deposit; ?></td>
    <td><?php echo $return; ?></td>
    <td><?php echo number_format($refund, 2); ?></td>
  </tr>
</table>

<?php include("footer.php"); ?>
