<?php 
include("db_connect.php"); 
include("header.php"); 
?>

<h2>📦 Stock Log</h2>

<table>
  <tr>
    <th>Date</th>
    <th>Total Deposits</th>
    <th>Total Returns</th>
    <th>Balance</th>
  </tr>
  <?php
  // Query: get deposits and returns grouped by date
  $sql = "
    SELECT 
        d.deposit_date AS log_date,
        COALESCE(SUM(d.quantity),0) AS total_deposit,
        COALESCE((
            SELECT SUM(r.quantity) 
            FROM return_date r 
            WHERE DATE(r.return_date) = DATE(d.deposit_date)
        ),0) AS total_return
    FROM deposit d
    GROUP BY DATE(d.deposit_date)
    ORDER BY d.deposit_date DESC
  ";

  
  ?>
</table>

<?php include("footer.php"); ?>
