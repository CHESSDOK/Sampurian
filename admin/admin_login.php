<?php
session_start();
include '../include/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['admin_password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = trim($admin['first_name'] . ' ' . $admin['last_name']);
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sampiruhan Online Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('https://media.istockphoto.com/id/182727706/photo/united-states-capitol-with-senate-chamber-under-blue-sky.jpg?s=1024x1024&w=is&k=20&c=So5rQmTir6AAingoTZrC40gC8k6O2GeDe0bEaOBmFII=') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      display: flex;
      width: 1000px;
      max-width: 3000px;
      height: 500px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .login-left {
      flex: 1;
      background: linear-gradient(to bottom right, #258B8C, #2EAEAF);
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }
    .login-left img {
      width: 200px;
      margin-bottom: 20px;
    }
    .login-left h4 {
      font-weight: bold;
    }
    .social-icons a {
      margin: 0 5px;
      color: white;
      font-size: 24px;
    }
    .login-right {
      flex: 1;
      background: #1c1c1c;
      color: white;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .login-right h3 {
      margin-bottom: 20px;
    }
    .form-control {
      background: #ffffffff;
      border: none;
      color: white;
    }
    .form-control:focus {
      background: #2c2c2c;
      color: white;
      box-shadow: none;
      border: 1px solid #DFF6F7;
    }
    .btn-login {
      background: #687EB1;
      border: none;
      width: 100%;
      padding: 10px;
      font-weight: bold;
      border-radius: 8px;
    }
    .btn-login:hover {
      background: #DFF6F7;
      color: #1c1c1c;
    }
    .forgot-link {
      font-size: 14px;
      text-align: right;
      margin-top: 10px;
      color: #aaa;
      display: block;
    }
    /* Mobile View */
@media (max-width: 768px) {
  /* Login Page */
  .login-container {
    flex-direction: column;
    width: 95%;
    height: auto;
    margin: 20px;
  }

  .login-left, .login-right {
    flex: none;
    width: 100%;
    padding: 20px;
    text-align: center;
  }

  .login-left img {
    width: 150px;
  }

  .login-right h1 {
    font-size: 22px;
  }

  .form-control {
    font-size: 14px;
    padding: 8px;
  }

  .btn-login {
    padding: 10px;
    font-size: 14px;
  }

  /* Registration Form */
  .form-container {
    width: 95%;
    padding: 20px;
    margin: 20px;
  }

  table {
    display: block;
    width: 100%;
  }

  td {
    display: block;
    width: 100%;
    padding: 6px 0;
  }

  .td-gap-right {
    padding-right: 0;
  }

  input, select {
    font-size: 14px;
    padding: 8px;
  }

  input[type="checkbox"] {
    margin-left: 0;
  }

  .submit-btn,
  .resend-btn {
    font-size: 14px;
    padding: 10px;
  }

  #otp {
    font-size: 14px;
    letter-spacing: 6px;
  }
}

  </style>
</head>
<body>
    <form method="POST" id="loginForm" novalidate>
    <div class="login-container">
        <!-- Left Side -->
        <div class="login-left text-center">
        <img src="../assets/image/sam.png" alt="Logo">
        <h1>Welcome<br>ADMIN</h1>
        <div class="social-icons mt-4">
        </div>
        </div>
        <!-- Right Side -->
        <div class="login-right">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        <h1 class="fw-bold">Barangay <span class="text-info">Sampiruhan</span></h1>

            <div class="mb-3">
            <input type="text" class="form-control" placeholder="email" name="email" required>
            </div>
            <div class="mb-3">
            <input type="password" class="form-control" placeholder="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login mt-3">Login</button>
        </div>
    </div>

    <!-- Bootstrap JS + Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    </form>
</body>
</html>

