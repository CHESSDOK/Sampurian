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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $current_time = date("Y-m-d H:i:s");
    
    // Check if user exists and is not verified
    $check = $db->prepare("SELECT id, otp_expiry FROM users WHERE email = ? AND is_verified = 0");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if previous OTP is still valid (prevent spam)
        if ($user['otp_expiry'] > $current_time) {
            $time_left = strtotime($user['otp_expiry']) - time();
            $minutes_left = ceil($time_left / 60);
            
            echo json_encode(['success' => false, 'message' => "Please wait for the current OTP to expire. You can request a new one in $minutes_left minutes."]);
            exit;
        }
        
        // Generate new OTP
        $new_otp = rand(100000, 999999);
        $new_expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
        
        // Update OTP in database
        $update = $db->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $new_otp, $new_expiry, $email);
        
        if ($update->execute()) {
            // Send new OTP email using PHPMailer
            $mail = new PHPMailer(true);
            
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pesolosbanos4@gmail.com'; // SMTP username
                $mail->Password   = 'rooy awbq emme qqyt'; // SMTP password (use app password for Gmail)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                
                // Recipients
                $mail->setFrom('noreply@yourdomain.com', 'Your Website Name');
                $mail->addAddress($email);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your New OTP Code';
                $mail->Body    = 'Your new OTP code is: <b>' . $new_otp . '</b><br>This code will expire in 5 minutes.';
                $mail->AltBody = 'Your new OTP code is: ' . $new_otp . '. This code will expire in 5 minutes.';
                
                $mail->send();
                echo json_encode(['success' => true, 'message' => 'New OTP sent to your email. Please check your inbox.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'OTP could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error generating new OTP.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found or already verified.']);
    }
}
?>