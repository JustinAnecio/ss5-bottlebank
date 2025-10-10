<?php include("db_connect.php"); ?>
<?php include("header.php"); ?>

<h2>📦 Stock Log</h2>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>Date</th>
    <th>Total Deposits</th>
    <th>Total Returns</th>
    <th>Total Refunds (₱)</th>
  </tr>

<?php
$sql = "
  SELECT log_date FROM (
    SELECT DATE(created_at) AS log_date FROM deposit
    UNION
    SELECT DATE(created_at) AS log_date FROM returns
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

    $deposit_total = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM deposit WHERE DATE(created_at)='$date'")->fetch_assoc()['total'];
    $return_total = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM returns WHERE DATE(created_at)='$date'")->fetch_assoc()['total'];
    $refund_total = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM refund WHERE DATE(refund_date)='$date'")->fetch_assoc()['total'];

    echo "<tr>
      <td>$date</td>
      <td>$deposit_total</td>
      <td>$return_total</td>
      <td>".number_format($refund_total,2)."</td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='4'>No records found</td></tr>";
}
?>
</table>

<?php include("footer.php"); ?>
