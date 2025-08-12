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
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f4f7fb; }
.login-box { max-width:420px; margin:8vh auto; background:#fff; padding:28px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06);}
</style>
</head>
<body>
<div class="login-box">
    <h4 class="mb-3 text-center">Admin Login</h4>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>
</html>
