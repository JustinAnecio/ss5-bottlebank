<?php 
include("db_connect.php");
include("header.php"); 
 
?>

<h2>📦 Stock Log</h2>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>Date</th>
    <th>Total Deposits</th>
    <th>Total Returns</th>
    <th>Total Refunds</th>
  </tr>

  <?php
  // Combine all dates from deposit, returns, and refund tables
  $sql = "
    SELECT log_date FROM (
      SELECT DATE(deposit_date) AS log_date FROM deposit
      UNION
      SELECT DATE(return_date) AS log_date FROM returns
      UNION
      SELECT DATE(refund_date) AS log_date FROM refund
    ) AS all_dates
    GROUP BY log_date
    ORDER BY log_date DESC
  ";

  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $date = $row['log_date'];

      // total deposits
      $deposit = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM deposit WHERE DATE(deposit_date)='$date'");
      $deposit_total = $deposit->fetch_assoc()['total'];

      // total returns
      $return = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM returns WHERE DATE(return_date)='$date'");
      $return_total = $return->fetch_assoc()['total'];

      // total refunds
      $refund = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM refund WHERE DATE(refund_date)='$date'");
      $refund_total = $refund->fetch_assoc()['total'];

      // balance = deposit - return - refund
      $balance = $deposit_total - $return_total - $refund_total;

      echo "<tr>";
      echo "<td>$date</td>";
      echo "<td>".number_format($deposit_total,2)."</td>";
      echo "<td>".number_format($return_total,2)."</td>";
      echo "<td>".number_format($refund_total,2)."</td>";
      echo "</tr>";
    }
  } else {
    echo "<tr><td colspan='5'>No records found</td></tr>";
  }
  ?>
</table>

<?php include("footer.php"); ?>
