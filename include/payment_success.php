<?php
session_start();
require_once 'config.php';

$permit_id = $_GET['permit_id'] ?? null;
$request_type = $_GET['type'] ?? null;

if ($permit_id && $request_type) {
    // Tables mapping
    $tables = [
        "indigency" => [
            "table" => "indigency",
            "id_field" => "permit_id",
            "purpose_field" => "nature_of_assistance",
            "amount" => 500,
            "payment_field" => "payment_type"
        ],
        "business" => [
            "table" => "business_permit",
            "id_field" => "permit_id",
            "purpose_field" => "nature_of_business",
            "amount" => 1000,
            "payment_field" => "payment_type"
        ],
        "animal_bite" => [
            "table" => "animal_bite_reports",
            "id_field" => "permit_id",
            "purpose_field" => "animal_description",
            "amount" => 300,
            "payment_field" => "payment_method"
        ],
        "clearance" => [
            "table" => "barangay_clearance",
            "id_field" => "permit_id",
            "purpose_field" => "purpose",
            "amount" => 500,
            "payment_field" => "payment_type"
        ],
        "renew" => [
            "table" => "business_permit_renewal",
            "id_field" => "permit_id",
            "purpose_field" => "nature_of_business",
            "amount" => 500,
            "payment_field" => "payment_type"
        ]
    ];

    if (!isset($tables[$request_type])) {
        $_SESSION['error_message'] = "⚠️ Invalid request type: $request_type";
        header("Location: ../dashboard.php");
        exit();
    }

    $config = $tables[$request_type];
    $table = $config['table'];
    $id_field = $config['id_field'];

    // ✅ Update status to paid
    $stmt = $pdo->prepare("UPDATE `$table` SET status = 'paid' WHERE `$id_field` = ?");
    $stmt->execute([$permit_id]);

    // ✅ Set success message
    $_SESSION['success_message'] = "Payment successful! Your receipt has been downloaded.";

    // ✅ Generate PDF download in new tab and redirect main window
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Payment Processing</title>
        <script>
            window.onload = function() {
                // Open PDF in new tab
                window.open('generate_receipt.php?permit_id=$permit_id&type=$request_type', '_blank');
                // Redirect main window to dashboard
                window.location.href = '../dashboard.php';
            }
        </script>
    </head>
    <body>
        <p>Processing your payment... Please wait.</p>
    </body>
    </html>";
    exit();
}

$_SESSION['error_message'] = "❌ permit_id or type missing in URL.";
header("Location: ../dashboard.php");
exit();