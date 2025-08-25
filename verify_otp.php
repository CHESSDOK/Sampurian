<?php
// Set Philippines timezone
date_default_timezone_set('Asia/Manila');

$db = new mysqli('localhost', 'root', '', 'sampurihan');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    $current_time = date("Y-m-d H:i:s");
    
    // Check if OTP is valid and not expired
    $stmt = $db->prepare("SELECT id, otp_expiry FROM users WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if OTP is expired
        if ($current_time > $user['otp_expiry']) {
            echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
        } else {
            // Mark user as verified
            $update = $db->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
            $update->bind_param("s", $email);
            
            if ($update->execute()) {
                echo json_encode(['success' => true, 'message' => 'Email verified successfully! Your account is now active.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error verifying account.']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP code.']);
    }
}
?>