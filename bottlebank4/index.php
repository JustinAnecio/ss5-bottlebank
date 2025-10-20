<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); exit;
}
$user_id = intval($_SESSION['user_id']);
$username = htmlspecialchars($_SESSION['username'] ?? 'User');

// helper to count
function count_for_user($conn, $table, $user_id){
  $allowed = ['deposit','returns','refund','stock_log'];
  if (!in_array($table,$allowed)) return 0;
  $sql = "SELECT COUNT(*) AS cnt FROM `$table` WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i',$user_id);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  return intval($res['cnt'] ?? 0);
}

$deposit_count = count_for_user($conn,'deposit',$user_id);
$return_count  = count_for_user($conn,'returns',$user_id);
$refund_count  = count_for_user($conn,'refund',$user_id);
$log_count     = count_for_user($conn,'stock_log',$user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard • BottleBank</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="asset/style.css">
</head>
<body>
  <div class="app">
    <div class="topbar">
      <div class="brand">
        <div class="logo">BB</div>
        <div>
          <h1>BottleBank</h1>
          <p class="kv">Smart bottle collection & tracking</p>
        </div>
      </div>

      <div class="menu-wrap">
        <div class="kv">Signed in as <strong><?= $username ?></strong></div>
        <div class="menu-body">
          <button class="menu-btn" onclick="toggleMenu()">
            <svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            Menu
          </button>

          <div class="menu-panel" id="menuPanel" aria-hidden="true">
            <a href="deposit.php">➕ Deposit</a>
            <a href="returns.php">🔁 Return</a>
            <a href="refund.php">💸 Refund</a>
            <a href="stock_log.php">📦 Stock Log</a>
            <a href="logout.php" style="color:#d64545">⎋ Logout</a>
          </div>
        </div>
      </div>
    </div>

    <div class="grid">
      <div class="card">
        <div class="label">Deposits</div>
        <div class="value"><?= $deposit_count ?></div>
        <a class="link" href="deposit.php">Add / View Deposits →</a>
      </div>

      <div class="card">
        <div class="label">Returns</div>
        <div class="value"><?= $return_count ?></div>
        <a class="link" href="returns.php">Add / View Returns →</a>
      </div>

      <div class="card">
        <div class="label">Refunds</div>
        <div class="value"><?= $refund_count ?></div>
        <a class="link" href="refund.php">Add / View Refunds →</a>
      </div>

      <div class="card">
        <div class="label">Stock Log</div>
        <div class="value"><?= $log_count ?></div>
        <a class="link" href="stock_log.php">View Log →</a>
      </div>

      <!-- main panel -->
      <div class="panel" style="grid-column: span 8;">
        <h3 style="margin-top:0">Overview</h3>
        <p class="hint">Use the menu to add deposits, returns or refunds. The stock log shows a combined timeline.</p>

        <div style="display:flex;gap:12px;margin-top:16px;flex-wrap:wrap">
          <div style="flex:1;min-width:220px;background:#f7fcfb;padding:12px;border-radius:10px">
            <!-- 📋 Recent Activity Section -->
<div style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 6px rgba(0,0,0,0.05);margin-top:20px;">
  <h2 style="margin:0 0 15px 0;font-size:18px;color:#223;">Recent Activity</h2>

  <?php
    

    // ✅ Query for combined recent activities
    $query = "
      (SELECT 'Deposit' AS type, deposit_date AS date, 
        CONCAT(quantity, ' bottles deposited (', bottle_type, ')') AS details 
       FROM deposit WHERE user_id = ?)
      UNION
      (SELECT 'Return' AS type, return_date AS date, 
        CONCAT(quantity, ' bottles returned (', bottle_type, ')') AS details 
       FROM returns WHERE user_id = ?)
      UNION
      (SELECT 'Refund' AS type, refund_date AS date, 
        CONCAT('Refunded ₱', amount) AS details 
       FROM refund WHERE user_id = ?)
      UNION
      (SELECT 'Stock Log' AS type, date_logged AS date, 
        CONCAT(action_type, ' — ', quantity, ' bottles (₱', amount, ')') AS details 
       FROM stock_log WHERE user_id = ?)
      ORDER BY date DESC
      LIMIT 10
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
  ?>

  <!-- Scrollable Table -->
  <div style="max-height:250px;overflow-y:auto;border:1px solid #e5e9ec;border-radius:8px;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <thead style="position:sticky;top:0;background:#f8fafc;">
        <tr>
          <th style="text-align:left;padding:10px 12px;color:#333;font-weight:600;">Type</th>
          <th style="text-align:left;padding:10px 12px;color:#333;font-weight:600;">Date</th>
          <th style="text-align:left;padding:10px 12px;color:#333;font-weight:600;">Details</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php
              $type = htmlspecialchars($row['type']);
              $date = htmlspecialchars(date("M d, Y — h:i A", strtotime($row['date'])));
              $details = htmlspecialchars($row['details']);

              // Icons per type
              $icon = match($type) {
                'Deposit' => '💰',
                'Return'  => '🔁',
                'Refund'  => '💸',
                'Stock Log' => '📦',
                default => '📋'
              };

              // Type color theme
              $color = match($type) {
                'Deposit' => '#1e9f4a',
                'Return'  => '#0d6efd',
                'Refund'  => '#dc3545',
                'Stock Log' => '#6f42c1',
                default => '#555'
              };
            ?>
            <tr style="border-bottom:1px solid #eee;">
              <td style="padding:10px 12px;color:<?php echo $color; ?>;font-weight:600;">
                <?php echo "$icon $type"; ?>
              </td>
              <td style="padding:10px 12px;color:#666;"><?php echo $date; ?></td>
              <td style="padding:10px 12px;"><?php echo $details; ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="3" style="padding:12px;text-align:center;color:#666;">No recent activities yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Footer Link -->
  <div style="margin-top:12px;text-align:right;">
    <a href="stock_log.php" style="color:#0d6efd;font-weight:500;text-decoration:none;">View all →</a>
  </div>
</div>



          </div>
          <div style="flex:1;min-width:220px;background:#fff8f9;padding:12px;border-radius:10px">
            <div class="kv">Tips</div>
            <div class="hint" style="margin-top:8px">Keep customer name consistent to track repeat customers. Use Stock Log to audit transactions.</div>
          </div>
        </div>
      </div>

      <div class="side" style="grid-column: span 4;">
        <h4 style="margin-top:0">Quick Actions</h4>
        <p><a class="menu-link" href="deposit.php"><button class="primary" style="width:100%;">➕ New Deposit</button></a></p>
        <p><a class="menu-link" href="returns.php"><button class="ghost" style="width:100%;">🔁 Log Return</button></a></p>
        <p><a class="menu-link" href="refund.php"><button class="ghost" style="width:100%;">💸 Issue Refund</button></a></p>
      </div>
    </div>

    <div class="footer">© <?=date('Y')?> BottleBank — Built with care</div>
  </div>

<script>
function toggleMenu(){
  const p = document.getElementById('menuPanel');
  if (p.style.display === 'block'){ p.style.display = 'none'; p.setAttribute('aria-hidden','true'); }
  else { p.style.display = 'block'; p.setAttribute('aria-hidden','false'); }
}
// close menu if clicked outside
document.addEventListener('click', function(e){
  const panel = document.getElementById('menuPanel');
  const btn = document.querySelector('.menu-btn');
  if (!panel) return;
  if (!panel.contains(e.target) && !btn.contains(e.target)) panel.style.display='none';
});
</script>
</body>
</html>
