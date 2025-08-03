<?php
// submit_business_permit.php
session_start();
require_once 'config.php'; // Make sure this contains your database connection details

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$new_sql = "SELECT * FROM MyGuests WHERE id = '$user_id'";
$result = $conn->query($new_sql);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Escape and sanitize form data
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $middle_name = $conn->real_escape_string($_POST['middle_name']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $status = $conn->real_escape_string($_POST['status']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $house_no = $conn->real_escape_string($_POST['house_no']);
    $street = $conn->real_escape_string($_POST['street']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $email = $conn->real_escape_string($_POST['email']);
    $establishment_name = $conn->real_escape_string($_POST['establishment_name']);
    $business_nature = $conn->real_escape_string($_POST['business_nature']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $gcash_ref_no = isset($_POST['gcash_ref_no']) ? $conn->real_escape_string($_POST['gcash_ref_no']) : '';
    
    // File upload handling
    $upload_dir = "include/uploads/business_permits/";
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Process file uploads
    $doc_business_reg = uploadFile('doc_business_reg', $upload_dir);
    $doc_cedula = uploadFile('doc_cedula', $upload_dir);
    $doc_others = uploadFile('doc_others', $upload_dir);
    $payment_proof = uploadFile('payment_proof', $upload_dir);
    
    // Insert into database
    $sql = "INSERT INTO business_permits (
        user_id, last_name, first_name, middle_name, dob, status, gender, 
        house_no, street, mobile, email, establishment_name, business_nature,
        doc_business_reg, doc_cedula, doc_others, payment_method, gcash_ref_no, 
        payment_proof, status, created_at
    ) VALUES (
        '$user_id', '$last_name', '$first_name', '$middle_name', '$dob', '$status', '$gender',
        '$house_no', '$street', '$mobile', '$email', '$establishment_name', '$business_nature',
        '$doc_business_reg', '$doc_cedula', '$doc_others', '$payment_method', '$gcash_ref_no',
        '$payment_proof', 'pending', NOW()
    )";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Business permit application submitted successfully!";
        header("Location: dashboard.php");
    } else {
        $_SESSION['error_message'] = "Error: " . $sql . "<br>" . $conn->error;
        header("Location: business_permit.php");
    }
    
    $conn->close();
    exit();
}

function uploadFile($field_name, $upload_dir) {
    if (isset($_FILES[$field_name]) {
        $file = $_FILES[$field_name];
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                return $file_path;
            }
        }
    }
    return '';
}
?>