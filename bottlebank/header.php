<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BottleBank</title>
<style>
body {
  font-family: Arial, sans-serif;
  margin: 0;
  background: #f8f8f8;
}
header {
  background: #2196f3;
  color: white;
  padding: 10px;
}
#menuToggle {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
}
#sideMenu {
  display: none;
  position: absolute;
  top: 50px;
  left: 10px;
  background: white;
  border: 1px solid #ccc;
  padding: 8px;
  z-index: 1000;
}
#sideMenu a {
  display: block;
  color: #333;
  padding: 6px 10px;
  text-decoration: none;
}
#sideMenu a:hover {
  background: #eee;
}
#mainContent {
  padding: 20px;
}
</style>
</head>
<body>
<header>
  <button id="menuToggle">☰</button>
  BottleBank
</header>

<nav id="sideMenu">
  <a href="index.php" class="menu-link">Home</a>
  <a href="user.php" class="menu-link">user</a>
  <a href="deposit.php" class="menu-link">Deposits</a>
  <a href="return.php" class="menu-link">Returns</a>
  <a href="refund.php" class="menu-link">Refunds</a>
  <a href="stock_log.php" class="menu-link">Stock Log</a>
</nav>
