<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Barangay Sampiruhan E-Permit System</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <div class="container">
        <img src="logo.png" alt="Barangay Logo">
        <h2>Barangay Sampiruhan<br>E- Permit System</h2>
        <form action="include/login_auth.php" method="POST" id="loginForm">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
            <a href="#" class="link">Forgot Password</a>
            <button onclick="window.location.href='registration.php'; return false;">Create Account</button>
        </form>
    </div>

</body>

</html>