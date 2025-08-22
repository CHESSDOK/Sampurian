<?php
// include/submit_business_permit.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];

        // Get user information to create directory
        $stmt = $pdo->prepare("SELECT f_name, m_name, l_name FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Create directory path
        $base_dir = "documents/";
        $user_dir = $user['l_name'] . ", " . $user['f_name'] . " " . $user['m_name'];
        $upload_dir = $base_dir . $user_dir . "/clearance/";

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique permit ID
        $permit_id = "BP-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(6)));

        // File upload handling with error checking
        $pic2x2 = uploadFile('picture', $upload_dir, '2x2');
        $payment_proof = isset($_FILES['payment_proof']) ? uploadFile('payment_proof', $upload_dir, 'Payment_Proof') : '';

        // Get form data
        $year_stay = $_POST['year_stay'];
        $purpose = $_POST['purpose'];
        $payment_type = $_POST['payment_method'];
        $gcash_ref_no = isset($_POST['gcash_ref_no']) ? $_POST['gcash_ref_no'] : '';

        // Insert into database
        $sql = "INSERT INTO barangay_clearance (
            permit_id, years_stay_in_barangay, purpose, 
            attachment, payment_proof,
            user_id, payment_type, gcash_ref_no, created_at
        ) VALUES (
            :permit_id, :year_stay, :purpose,
            :pic2x2, :payment_proof,
            :user_id, :payment_type, :gcash_ref_no, NOW()
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':permit_id' => $permit_id,
            ':year_stay' => $year_stay,
            ':purpose' => $purpose,
            ':pic2x2' => $pic2x2,
            ':payment_proof' => $payment_proof,
            ':user_id' => $user_id,
            ':payment_type' => $payment_type,
            ':gcash_ref_no' => $gcash_ref_no
        ]);

        // Create notification
        $message = "Your barangay clearance application (ID: $permit_id) has been submitted successfully.";

        $_SESSION['success_message'] = "barangay clearance application submitted successfully! Your permit ID is: $permit_id";
        header("Location: ../dashboard.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: ../clearance.php");
        exit();
    }
}

function uploadFile($field_name, $upload_dir, $file_prefix)
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        if ($_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE && $field_name !== 'doc_others' && $field_name !== 'payment_proof') {
            $_SESSION['error_message'] = "Required file is missing: " . $field_name;
            header("Location: ../clearance.php");
            exit();
        }
        return '';
    }

    $file = $_FILES[$field_name];
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = $file_prefix . "_" . time() . "." . $file_ext;
    $file_path = $upload_dir . $file_name;

    // Check file size (max 5MB)
    if ($file['size'] > 5000000) {
        $_SESSION['error_message'] = "File size too large. Maximum size is 5MB.";
        header("Location: ../clearance.php");
        exit();
    }

    // Allow only certain file types
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        $_SESSION['error_message'] = "Only PDF, JPG, JPEG, PNG files are allowed.";
        header("Location: ../clearance.php");
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    } else {
        $_SESSION['error_message'] = "Error uploading file.";
        header("Location: ../clearance.php");
        exit();
    }
}
