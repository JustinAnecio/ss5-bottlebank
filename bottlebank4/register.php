<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if username or email already exists
  $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
  $check->bind_param("ss", $username, $email);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    $error = "Username or email already taken.";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
      header("Location: login.php?registered=1");
      exit();
    } else {
      $error = "Registration failed. Try again.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | BottleBank</title>
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

    .register-container {
      background: #fff;
      padding: 40px 50px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      width: 380px;
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

    .login-link {
      margin-top: 15px;
      display: block;
      color: #555;
    }

    .login-link a {
      color: #0077cc;
      text-decoration: none;
      font-weight: 600;
    }

    .login-link a:hover {
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
  <div class="register-container">
    <h2>Create Account</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Register</button>
      <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </form>
  </div>
</body>
</html>
