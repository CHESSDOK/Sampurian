<?php
$db = new mysqli('localhost', 'root', '', 'sampurihan');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $f_name = sanitize($_POST['f_name']);
    $m_name = sanitize($_POST['m_name']);
    $l_name = sanitize($_POST['l_name']);
    $email = sanitize($_POST['email']);
    $contact = sanitize($_POST['contact']);
    $birthday = sanitize($_POST['birthday']);
    $gender = sanitize($_POST['gender']);
    $marriage_status = sanitize($_POST['marriage_status']);
    $address = sanitize($_POST['address']);
    $password = $_POST['user_password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email exists
    $check = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists.'); window.history.back();</script>";
        exit;
    }

    // Upload picture
    $picture_name = "";
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $picture_name = basename($_FILES["picture"]["name"]);
        $target_file = $target_dir . time() . "_" . $picture_name;
        move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
        $picture_name = $target_file;
    }

    $stmt = $db->prepare("INSERT INTO users (email, user_password, f_name, m_name, l_name, birthday, contact, marriage_status, gender, address, picture) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $email, $hashed_password, $f_name, $m_name, $l_name, $birthday, $contact, $marriage_status, $gender, $address, $picture_name);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location.href='../index.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
