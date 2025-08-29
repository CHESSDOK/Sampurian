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

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique permit ID
        $permit_id = "BP-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(6)));

        // File uploads
        $business_reg   = uploadFile('doc_business_reg', $upload_dir, 'Business_Registration');
        $cedula         = uploadFile('doc_cedula', $upload_dir, 'Cedula');
        $barangay_reqs  = uploadFile('doc_others', $upload_dir, 'Barangay_Requirements');
        $payment_proof  = isset($_FILES['payment_proof']) ? uploadFile('payment_proof', $upload_dir, 'Payment_Proof') : '';

        // Get form data
        $kind_of_establishment = $_POST['establishment_name'];
        $nature_of_business    = $_POST['business_nature'];
        $payment_type          = $_POST['payment_method'];
        $gcash_ref_no          = isset($_POST['gcash_ref_no']) ? $_POST['gcash_ref_no'] : '';

        if ($payment_type === "Online") {
            // ✅ Create PayMongo Checkout Session
            $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Basic ' . base64_encode('sk_test_RtRn2nPog8rdTZu1Pdw2KoXo:'), 
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'data' => [
                        'attributes' => [
                            'line_items' => [[
                                'name'     => 'Barangay Business Permit',
                                'quantity' => 1,
                                'amount'   => 50000, // ₱500.00 (in centavos)
                                'currency' => 'PHP'
                            ]],
                            'payment_method_types' => ['gcash', 'paymaya', 'grab_pay', 'card'],
                            'success_url' => "http://localhost/project/include/payment_success.php?permit_id=$permit_id",
                            'cancel_url'  => "http://localhost/project/include/payment_failed.php?permit_id=$permit_id"
                        ]
                    ]
                ]),
            ]);

            $response = curl_exec($ch);
            curl_close($ch);
            $payload = json_decode($response, true);

            if (isset($payload['data']['attributes']['checkout_url'])) {
                $checkoutUrl = $payload['data']['attributes']['checkout_url'];

                // Save as pending
                $sql = "INSERT INTO business_permit (
                    permit_id, kind_of_establishment, nature_of_business,
                    business_registration, cedula, barangay_requirements, payment_proof,
                    user_id, payment_type, gcash_ref_no, created_at, status
                ) VALUES (
                    :permit_id, :kind_of_establishment, :nature_of_business,
                    :business_reg, :cedula, :barangay_reqs, :payment_proof,
                    :user_id, :payment_type, :gcash_ref_no, NOW(), 'pending'
                )";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':permit_id' => $permit_id,
                    ':kind_of_establishment' => $kind_of_establishment,
                    ':nature_of_business' => $nature_of_business,
                    ':business_reg' => $business_reg,
                    ':cedula' => $cedula,
                    ':barangay_reqs' => $barangay_reqs,
                    ':payment_proof' => $payment_proof,
                    ':user_id' => $user_id,
                    ':payment_type' => $payment_type,
                    ':gcash_ref_no' => $gcash_ref_no
                ]);

                // 🔗 Redirect user to PayMongo checkout page
                echo "<script>window.open('$checkoutUrl', '_blank'); window.location.href='../dashboard.php';</script>";
                exit;
            } else {
                $_SESSION['error_message'] = "❌ Failed to create PayMongo checkout session.";
                header("Location: ../barangay_permit.php");
                exit();
            }
        } else {
            // ✅ Cash or manual payment
            $sql = "INSERT INTO business_permit (
                permit_id, kind_of_establishment, nature_of_business, 
                business_registration, cedula, barangay_requirements, payment_proof,
                user_id, payment_type, gcash_ref_no, created_at, status
            ) VALUES (
                :permit_id, :kind_of_establishment, :nature_of_business,
                :business_reg, :cedula, :barangay_reqs, :payment_proof,
                :user_id, :payment_type, :gcash_ref_no, NOW(), 'unpaid'
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':permit_id' => $permit_id,
                ':kind_of_establishment' => $kind_of_establishment,
                ':nature_of_business' => $nature_of_business,
                ':business_reg' => $business_reg,
                ':cedula' => $cedula,
                ':barangay_reqs' => $barangay_reqs,
                ':payment_proof' => $payment_proof,
                ':user_id' => $user_id,
                ':payment_type' => $payment_type,
                ':gcash_ref_no' => $gcash_ref_no
            ]);

            $_SESSION['success_message'] = "Business permit submitted! Please pay at the Barangay Hall.";
            header("Location: ../dashboard.php");
            exit();
        }
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

    if ($file['size'] > 5000000) {
        $_SESSION['error_message'] = "File size too large. Max 5MB.";
        header("Location: ../barangay_permit.php");
        exit();
    }

    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        $_SESSION['error_message'] = "Only PDF, JPG, JPEG, PNG allowed.";
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
