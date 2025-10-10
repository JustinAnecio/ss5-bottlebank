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
  display: flex;
  align-items: center;
}
#menuToggle {
  background: none;
  border: none;
  color: white;
  font-size: 22px;
  cursor: pointer;
  margin-right: 15px;
}
#sideMenu {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  background: white;
  width: 180px;
  height: 100%;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  z-index: 999;
}
#sideMenu a {
  display: block;
  padding: 12px;
  color: #333;
  text-decoration: none;
  border-bottom: 1px solid #eee;
}
#sideMenu a:hover {
  background: #e3f2fd;
}
#mainContent {
  padding: 20px;
}
</style>
</head>
<body>
<header>
  <button id="menuToggle">☰</button>
  <h2 style="margin:0;">BottleBank System</h2>
</header>

<nav id="sideMenu">
  <a href="index.php">Home</a>
  <a href="deposit.php">Deposits</a>
  <a href="return.php">Returns</a>
  <a href="refund.php">Refunds</a>
  <a href="stock_log.php">Stock Log</a>
</nav>

<div id="mainContent">
