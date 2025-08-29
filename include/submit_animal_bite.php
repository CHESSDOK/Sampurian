<?php
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
        $upload_dir = $base_dir . $user_dir . "/animal_bite_reports/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique report ID
        $report_id = "ABR-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(6)));

        // File upload handling
        $payment_proof = isset($_FILES['payment_proof']) ? uploadFile('payment_proof', $upload_dir, 'Payment_Proof') : '';

        // Process animal conditions (checkboxes)
        $animal_conditions = isset($_POST['condition']) ? $_POST['condition'] : [];
        $animal_conditions_json = json_encode($animal_conditions);

        // Victim info
        $victim_data = [
            'last_name' => $_POST['last_name'],
            'first_name' => $_POST['first_name'],
            'middle_name' => $_POST['middle_name'],
            'dob' => $_POST['dob'],
            'age' => $_POST['age'],
            'gender' => $_POST['gender'],
            'contact' => $_POST['contact'],
            'guardian' => $_POST['guardian']
        ];

        // Bite details
        $bite_data = [
            'bite_location' => $_POST['bite_location'],
            'body_part' => $_POST['body_part'],
            'washed' => $_POST['washed'],
            'bite_date' => $_POST['bite_date'],
            'animal_description' => $_POST['animal_description'],
            'color' => $_POST['color'],
            'marks' => $_POST['marks'],
            'animal_condition' => $animal_conditions_json,
            'registered' => $_POST['registered'],
            'other_animals' => $_POST['other_animals'],
            'dog_condition' => $_POST['dog_condition'],
            'owner_name' => $_POST['owner_name']
        ];

        // Payment info
        $payment_type = $_POST['payment_method'];
        $gcash_ref_no = isset($_POST['gcash_ref_no']) ? $_POST['gcash_ref_no'] : null;

        // If online payment (PayMongo Checkout)
        if ($payment_type === 'online') {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/checkout_sessions");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);

            $data = [
                "data" => [
                    "attributes" => [
                        "cancel_url" => "http://localhost/project/animal_bite.php",
                        "success_url" => "http://localhost/project/success_redirect.php?report_id=$report_id",
                        "description" => "Animal Bite Report Payment - $report_id",
                        "line_items" => [[
                            "name" => "Animal Bite Report Fee",
                            "quantity" => 1,
                            "amount" => 10000, // Amount in centavos (₱100.00)
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
                // Save report info first before redirect
                $sql = "INSERT INTO animal_bite_reports (
                    permit_id, user_id, 
                    last_name, first_name, middle_name, dob, age, gender, contact, guardian,
                    bite_location, body_part, washed, bite_date, animal_description, color, marks,
                    animal_condition, registered, other_animals, dog_condition, owner_name,
                    payment_method, gcash_ref_no, payment_proof, created_at
                ) VALUES (
                    :report_id, :user_id,
                    :last_name, :first_name, :middle_name, :dob, :age, :gender, :contact, :guardian,
                    :bite_location, :body_part, :washed, :bite_date, :animal_description, :color, :marks,
                    :animal_condition, :registered, :other_animals, :dog_condition, :owner_name,
                    :payment_method, :gcash_ref_no, :payment_proof, NOW()
                )";

                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge(
                    ['report_id' => $report_id, 'user_id' => $user_id],
                    $victim_data,
                    $bite_data,
                    [
                        'payment_method' => $payment_type,
                        'gcash_ref_no' => $gcash_ref_no,
                        'payment_proof' => $payment_proof
                    ]
                ));

                // Redirect to PayMongo checkout
                header("Location: " . $result['data']['attributes']['checkout_url']);
                exit();
            } else {
                $_SESSION['error_message'] = "Error creating PayMongo Checkout Session.";
                header("Location: ../animal_bite.php");
                exit();
            }
        } else {
            // For manual (GCash upload or other)
            $sql = "INSERT INTO animal_bite_reports (
                permit_id, user_id, 
                last_name, first_name, middle_name, dob, age, gender, contact, guardian,
                bite_location, body_part, washed, bite_date, animal_description, color, marks,
                animal_condition, registered, other_animals, dog_condition, owner_name,
                payment_method, gcash_ref_no, payment_proof, created_at
            ) VALUES (
                :report_id, :user_id,
                :last_name, :first_name, :middle_name, :dob, :age, :gender, :contact, :guardian,
                :bite_location, :body_part, :washed, :bite_date, :animal_description, :color, :marks,
                :animal_condition, :registered, :other_animals, :dog_condition, :owner_name,
                :payment_method, :gcash_ref_no, :payment_proof, NOW()
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_merge(
                ['report_id' => $report_id, 'user_id' => $user_id],
                $victim_data,
                $bite_data,
                [
                    'payment_method' => $payment_type,
                    'gcash_ref_no' => $gcash_ref_no,
                    'payment_proof' => $payment_proof
                ]
            ));

            $_SESSION['success_message'] = "Animal bite report submitted successfully! Your report ID is: $report_id";
            header("Location: ../dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: ../animal_bite.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: ../animal_bite.php");
        exit();
    }
}

function uploadFile($field_name, $upload_dir, $file_prefix)
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        if ($_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE && $field_name !== 'payment_proof') {
            $_SESSION['error_message'] = "Required file is missing: " . $field_name;
            header("Location: ../animal_bite.php");
            exit();
        }
        return null;
    }

    $file = $_FILES[$field_name];
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = $file_prefix . "_" . time() . "." . $file_ext;
    $file_path = $upload_dir . $file_name;

    if ($file['size'] > 5000000) {
        $_SESSION['error_message'] = "File size too large. Maximum size is 5MB.";
        header("Location: ../animal_bite.php");
        exit();
    }

    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        $_SESSION['error_message'] = "Only PDF, JPG, JPEG, PNG files are allowed.";
        header("Location: ../animal_bite.php");
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    } else {
        $_SESSION['error_message'] = "Error uploading file.";
        header("Location: ../animal_bite.php");
        exit();
    }
}
