<?php 
include("db_connect.php"); 
include("header.php"); 

// Fetch stats
$userCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user"))['total'];
$deposit   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total FROM deposit"));
$return    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt, COALESCE(SUM(quantity),0) AS total FROM returns")); // ✅ FIXED
$refund    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total FROM refund"));
?>

<h2>📊 Reports Overview</h2>

<div class="grid">
  <div class="card blue">
    <h3>Total Users</h3>
    <p class="big"><?= number_format($userCount) ?></p>
  </div>
  <div class="card green">
    <h3>Total Deposits</h3>
    <p class="big"><?= number_format($deposit['cnt']) ?></p>
    <small>Bottles: <?= number_format($deposit['total']) ?></small>
  </div>
  <div class="card orange">
    <h3>Total Returns</h3>
    <p class="big"><?= number_format($return['cnt']) ?></p>
    <small>Bottles: <?= number_format($return['total']) ?></small>
  </div>
  <div class="card red">
    <h3>Total Refunds</h3>
    <p class="big"><?= number_format($refund['cnt']) ?></p>
    <small>Value: PHP <?= number_format($refund['total'], 2) ?></small>
  </div>
</div>

<?php include("footer.php"); ?>
