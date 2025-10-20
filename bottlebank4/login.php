<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION['user_id'] = $user_id;
      $_SESSION['username'] = $username;
      header("Location: index.php");
      exit();
    } else {
      $error = "Invalid password.";
    }
  } else {
    $error = "User not found.";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | BottleBank</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(135deg, #0077cc, #00c16e);
    }

    .login-container {
      background: #fff;
      padding: 40px 50px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      width: 350px;
      text-align: center;
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      margin-bottom: 20px;
      color: #0077cc;
    }

    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    label {
      font-weight: 600;
      color: #333;
      display: block;
      margin-bottom: 5px;
    }

    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      transition: 0.3s;
    }

    input:focus {
      border-color: #00c16e;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 193, 110, 0.3);
    }

    button {
      width: 100%;
      background: linear-gradient(135deg, #0077cc, #00c16e);
      color: white;
      font-weight: 600;
      border: none;
      padding: 12px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background: linear-gradient(135deg, #00b36b, #005fa3);
      transform: translateY(-2px);
    }

    .register-link {
      margin-top: 15px;
      display: block;
      color: #555;
    }

    .register-link a {
      color: #0077cc;
      text-decoration: none;
      font-weight: 600;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    .error {
      color: red;
      margin-bottom: 15px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>BottleBank Login</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Login</button>
      <div class="register-link">
        Don’t have an account? <a href="register.php">Register</a>
      </div>
    </form>
  </div>
</body>
</html>
