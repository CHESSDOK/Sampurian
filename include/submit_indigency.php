<?php
// include/submit_indigency.php
session_start();
require_once 'config.php';
// ✅ Load PayMongo SDK
require '../vendor/autoload.php';

use Paymongo\PaymongoClient;

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
                                'name'     => 'Barangay Indigency Certificate',
                                'quantity' => 1,
                                'amount'   => 50000, // ₱500.00
                                'currency' => 'PHP'
                            ]],
                            'payment_method_types' => ['gcash', 'paymaya', 'grab_pay', 'card'],
                            'success_url' => "http://localhost/project/include/payment_success.php?permit_id=$permit_id&type=indigency",
                            'cancel_url'  => "http://localhost/project/include/payment_failed.php?permit_id=$permit_id&type=indigency"
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

                // ✅ Insert notification for admin
               $message = "New Indigency request submitted by " . $user['f_name'] . " " . $user['l_name'];

                $notif_sql = "INSERT INTO notification 
                    (user_id, request_id, module, recipient_type, message, is_read, is_read_admin) 
                    VALUES (?, ?, 'indigency', 'admin', ?, 0, 0)";

                $pdo->prepare($notif_sql)->execute([$user_id, $permit_id, $message]);

                // Redirect to PayMongo Checkout
                header("Location: " . $checkoutUrl);
                exit;
            } else {
                $_SESSION['error_message'] = "❌ Failed to create checkout session.";
                header("Location: ../indigency.php");
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
                ':permit_id'   => $permit_id,
                ':purpose'     => $purpose,
                ':user_id'     => $user_id,
                ':payment_type'=> $payment_type
            ]);

            // ✅ Insert notification for admin
           $message = "New Indigency request submitted by " . $user['f_name'] . " " . $user['l_name'];

            $notif_sql = "INSERT INTO notification 
                (user_id, request_id, module, recipient_type, message, is_read, is_read_admin) 
                VALUES (?, ?, 'indigency', 'admin', ?, 0, 0)";

            $pdo->prepare($notif_sql)->execute([$user_id, $permit_id, $message]);


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
