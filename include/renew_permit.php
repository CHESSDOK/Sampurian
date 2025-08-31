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

        // Get user info
        $stmt = $pdo->prepare("SELECT f_name, m_name, l_name FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Directory path
        $base_dir = "documents/";
        $user_dir = $user['l_name'] . ", " . $user['f_name'] . " " . $user['m_name'];
        $upload_dir = $base_dir . $user_dir . "/Renew Business Permit/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Unique permit ID
        $permit_id = "BP-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(6)));

        // File uploads
        $business_reg = uploadFile('doc_business_reg', $upload_dir, 'Business_Registration');
        $cedula = uploadFile('doc_cedula', $upload_dir, 'Cedula');
        $business_Permit = uploadFile('doc_business_permit', $upload_dir, 'Business_Permit');
        $business_Permit_payment = uploadFile('doc_business_payment', $upload_dir, 'Business_Permit_Payment');
        $barangay_reqs = uploadFile('doc_others', $upload_dir, 'Barangay_Requirements');
        $payment_proof = isset($_FILES['payment_proof']) ? uploadFile('payment_proof', $upload_dir, 'Payment_Proof') : '';

        // Form data
        $kind_of_establishment = $_POST['establishment_name'];
        $nature_of_business = $_POST['business_nature'];
        $payment_type = $_POST['payment_method'];

        // ✅ If online payment via PayMongo
        if ($payment_type === 'online') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/checkout_sessions");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);

            $data = [
                "data" => [
                    "attributes" => [
                        "cancel_url" => "http://localhost/project/renew_permit.php",
                        "success_url" => "http://localhost/project/success_redirect.php?permit_id=$permit_id&type=renew",
                        "description" => "Business Permit Renewal - $permit_id",
                        "line_items" => [[
                            "name" => "Business Permit Renewal Fee",
                            "quantity" => 1,
                            "amount" => 50000, // ₱500.00 (amount in centavos)
                            "currency" => "PHP"
                        ]],
                        "payment_method_types" => ["gcash", "grab_pay", "card"]
                    ]
                ]
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode("YOUR_SECRET_KEY:")
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            if (isset($result['data']['attributes']['checkout_url'])) {
                // Save in DB before redirect
                $sql = "INSERT INTO business_permit_renewal (
                    permit_id, name_kind_of_establishment, nature_of_business, 
                    business_registration, cedula, barangay_requirements, business_permit, business_payment,
                    user_id, payment_type, created_at
                ) VALUES (
                    :permit_id, :kind_of_establishment, :nature_of_business,
                    :business_reg, :cedula, :barangay_reqs, :business_Permit, :business_Permit_payment,
                    :user_id, :payment_type, NOW()
                )";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':permit_id' => $permit_id,
                    ':kind_of_establishment' => $kind_of_establishment,
                    ':nature_of_business' => $nature_of_business,
                    ':business_reg' => $business_reg,
                    ':cedula' => $cedula,
                    ':barangay_reqs' => $barangay_reqs,
                    ':business_Permit' => $business_Permit,
                    ':business_Permit_payment' => $business_Permit_payment,
                    ':payment_proof' => $payment_proof,
                    ':user_id' => $user_id,
                    ':payment_type' => $payment_type,
                ]);

                header("Location: " . $result['data']['attributes']['checkout_url']);
                exit();
            } else {
                $_SESSION['error_message'] = "Error creating PayMongo Checkout Session.";
                header("Location: ../renew_permit.php");
                exit();
            }
        } else {
            // ✅ For manual submission
            $sql = "INSERT INTO business_permit_renewal (
                permit_id, name_kind_of_establishment, nature_of_business, 
                business_registration, cedula, barangay_requirements, business_permit, business_payment,
                user_id, payment_type, created_at
            ) VALUES (
                :permit_id, :kind_of_establishment, :nature_of_business,
                :business_reg, :cedula, :barangay_reqs, :business_Permit, :business_Permit_payment,
                :user_id, :payment_type, NOW()
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':permit_id' => $permit_id,
                ':kind_of_establishment' => $kind_of_establishment,
                ':nature_of_business' => $nature_of_business,
                ':business_reg' => $business_reg,
                ':cedula' => $cedula,
                ':barangay_reqs' => $barangay_reqs,
                ':business_Permit' => $business_Permit,
                ':business_Permit_payment' => $business_Permit_payment,
                ':payment_proof' => $payment_proof,
                ':user_id' => $user_id,
                ':payment_type' => $payment_type,
            ]);

            $_SESSION['success_message'] = "Business permit application submitted successfully! Your permit ID is: $permit_id";
            header("Location: ../dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: ../renew_permit.php");
        exit();
    }
}

function uploadFile($field_name, $upload_dir, $file_prefix)
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        if ($_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE && $field_name !== 'doc_others') {
            $_SESSION['error_message'] = "Required file is missing: " . $field_name;
            header("Location: ../renew_permit.php");
            exit();
        }
        return '';
    }

    $file = $_FILES[$field_name];
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = $file_prefix . "_" . time() . "." . $file_ext;
    $file_path = $upload_dir . $file_name;

    if ($file['size'] > 5000000) {
        $_SESSION['error_message'] = "File size too large. Maximum size is 5MB.";
        header("Location: ../renew_permit.php");
        exit();
    }

    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        $_SESSION['error_message'] = "Only PDF, JPG, JPEG, PNG files are allowed.";
        header("Location: ../renew_permit.php");
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    } else {
        $_SESSION['error_message'] = "Error uploading file.";
        header("Location: ../renew_permit.php");
        exit();
    }
}
