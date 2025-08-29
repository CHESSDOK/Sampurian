        <?php
        // include/submit_business_permit.php
        session_start();
        require_once 'config.php';
       // ✅ Load PayMongo SDK
        require '../vendor/autoload.php';

        use Paymongo\PaymongoClient;

        // Use your PayMongo secret key (test first)
        $paymongo = new PaymongoClient("sk_test_RtRn2nPog8rdTZu1Pdw2KoXo");

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $user_id = $_SESSION['user_id'];
                $stmt = $pdo->prepare("SELECT f_name, m_name, l_name FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $permit_id = "IND-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(4)));
                $purpose = $_POST['purpose'];
                $payment_type = $_POST['payment_method'];

            if ($payment_type === "Online") {
                // ✅ Create PayMongo Checkout Session via API
                $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_HTTPHEADER     => [
                        'Authorization: Basic ' . base64_encode('sk_test_RtRn2nPog8rdTZu1Pdw2KoXo:'), // secret key
                        'Content-Type: application/json'
                    ],
                    CURLOPT_POSTFIELDS => json_encode([
                        'data' => [
                            'attributes' => [
                                'line_items' => [[
                                    'name'     => 'Barangay Business Permit',
                                    'quantity' => 1,
                                    'amount'   => 50000, // ₱500.00 in centavos
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
                    $sql = "INSERT INTO indigency (
                        permit_id, nature_of_assistance, user_id, payment_type, created_at, status
                    ) VALUES (
                        :permit_id, :purpose, :user_id, :payment_type, NOW(), 'pending'
                    )";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':permit_id'    => $permit_id,
                        ':purpose'      => $purpose,
                        ':user_id'      => $user_id,
                        ':payment_type' => $payment_type
                    ]);

                    // Redirect to PayMongo Checkout
                    header("Location: " . $checkoutUrl);
                    exit;
                } else {
                    $_SESSION['error_message'] = "❌ Failed to create checkout session.";
                    header("Location: ../business_permit.php");
                    exit;
                }
            } else {
                    // ✅ Cash payment (save request directly)
                    $sql = "INSERT INTO indigency (
                        permit_id, nature_of_assistance, user_id, payment_type, created_at, status
                    ) VALUES (
                        :permit_id, :purpose, :user_id, :payment_type, NOW(), 'unpaid'
                    )";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':permit_id' => $permit_id,
                        ':purpose'   => $purpose,
                        ':user_id'   => $user_id,
                        ':payment_type' => $payment_type
                    ]);

                    $_SESSION['success_message'] = "Request submitted, pay at Barangay Hall.";
                    header("Location: ../dashboard.php");
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Error: " . $e->getMessage();
                header("Location: ../indigency.php");
                exit;
            }
        }

        function uploadFile($field_name, $upload_dir, $file_prefix)
        {
            if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
                if ($_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE && $field_name !== 'doc_others' && $field_name !== 'payment_proof') {
                    $_SESSION['error_message'] = "Required file is missing: " . $field_name;
                    header("Location: ../indigency.php");
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
                header("Location: ../indigency.php");
                exit();
            }

            // Allow only certain file types
            $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($file_ext), $allowed_types)) {
                $_SESSION['error_message'] = "Only PDF, JPG, JPEG, PNG files are allowed.";
                header("Location: ../indigency.php");
                exit();
            }

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                return $file_path;
            } else {
                $_SESSION['error_message'] = "Error uploading file.";
                header("Location: ../indigency.php");
                exit();
            }
        }
