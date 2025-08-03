<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sampurihan");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE BINARY email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify hashed password
    if (password_verify($password, $user['user_password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['f_name'] = $user['f_name'];
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "<script>alert('Incorrect password'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Email does not exist'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
