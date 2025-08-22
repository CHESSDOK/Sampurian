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
        $upload_dir = $base_dir . $user_dir . "/Business Permit/";

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique permit ID
        $permit_id = "BP-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(6)));

        // File upload handling with error checking
        $business_reg = uploadFile('doc_business_reg', $upload_dir, 'Business_Registration');
        $cedula = uploadFile('doc_cedula', $upload_dir, 'Cedula');
        $barangay_reqs = uploadFile('doc_others', $upload_dir, 'Barangay_Requirements');
        $payment_proof = isset($_FILES['payment_proof']) ? uploadFile('payment_proof', $upload_dir, 'Payment_Proof') : '';

        // Get form data
        $kind_of_establishment = $_POST['establishment_name'];
        $nature_of_business = $_POST['business_nature'];
        $payment_type = $_POST['payment_method'];
        $gcash_ref_no = isset($_POST['gcash_ref_no']) ? $_POST['gcash_ref_no'] : '';

        // Insert into database
        $sql = "INSERT INTO business_permit (
            permit_id, kind_of_establishment, nature_of_business, 
            business_registration, cedula, barangay_requirements, payment_proof,
            user_id, payment_type, gcash_ref_no, created_at
        ) VALUES (
            :permit_id, :kind_of_establishment, :nature_of_business, :payment_proof,
            :business_reg, :cedula, :barangay_reqs,
            :user_id, :payment_type, :gcash_ref_no, NOW()
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':permit_id' => $permit_id,
            ':kind_of_establishment' => $kind_of_establishment,
            ':nature_of_business' => $nature_of_business,
            ':payment_proof' => $payment_proof,
            ':business_reg' => $business_reg,
            ':cedula' => $cedula,
            ':barangay_reqs' => $barangay_reqs,
            ':user_id' => $user_id,
            ':payment_type' => $payment_type,
            ':gcash_ref_no' => $gcash_ref_no
        ]);

        // Create notification
        $message = "Your business permit application (ID: $permit_id) has been submitted successfully.";

        $_SESSION['success_message'] = "Business permit application submitted successfully! Your permit ID is: $permit_id";
        header("Location: ../dashboard.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: ../barangay_permit.php");
        exit();
    }
}

function uploadFile($field_name, $upload_dir, $file_prefix)
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        if ($_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE && $field_name !== 'doc_others' && $field_name !== 'payment_proof') {
            $_SESSION['error_message'] = "Required file is missing: " . $field_name;
            header("Location: ../barangay_permit.php");
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
        header("Location: ../barangay_permit.php");
        exit();
    }

    // Allow only certain file types
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        $_SESSION['error_message'] = "Only PDF, JPG, JPEG, PNG files are allowed.";
        header("Location: ../barangay_permit.php");
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    } else {
        $_SESSION['error_message'] = "Error uploading file.";
        header("Location: ../barangay_permit.php");
        exit();
    }
}
