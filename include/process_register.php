<?php
// Set Philippines timezone
date_default_timezone_set('Asia/Manila');

// Include Composer's autoloader
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
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

    // Generate OTP
    $otp_code = rand(100000, 999999);
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
    
    // Insert user with OTP (not verified yet)
    $stmt = $db->prepare("INSERT INTO users (email, user_password, f_name, m_name, l_name, birthday, contact, marriage_status, gender, address, picture, otp_code, otp_expiry) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $email, $hashed_password, $f_name, $m_name, $l_name, $birthday, $contact, $marriage_status, $gender, $address, $picture_name, $otp_code, $otp_expiry);

    if ($stmt->execute()) {
        // Send OTP email using PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ict1mercado.cdlb@gmail.com'; // SMTP username
            $mail->Password   = 'swnr plwx zscz yxce'; // SMTP password (use app password for Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Recipients
            $mail->setFrom('ict1mercado.cdlb@gmail.com', 'SAMPURIAN');
            $mail->addAddress($email);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code for Registration';
            $mail->Body    = 'Your OTP code is: <b>' . $otp_code . '</b><br>This code will expire in 5 minutes.';
            $mail->AltBody = 'Your OTP code is: ' . $otp_code . '. This code will expire in 5 minutes.';
            
            $mail->send();
            echo json_encode(['success' => true, 'message' => 'OTP sent to your email. Please check your inbox.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'OTP could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
}
?>