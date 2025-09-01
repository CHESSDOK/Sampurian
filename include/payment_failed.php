<?php
// include/payment_failed.php
session_start();
require_once 'config.php';

if (!isset($_GET['permit_id']) || !isset($_GET['type'])) {
    $_SESSION['error_message'] = "Invalid payment cancellation request.";
    header("Location: ../dashboard.php");
    exit();
}

$permit_id = $_GET['permit_id'];
$type      = $_GET['type'];

// Map type → database table + notification module
$table_map = [
    'business'     => ['table' => 'business_permit',           'module' => 'business_permit'],
    'renewal'      => ['table' => 'business_permit_renewal',   'module' => 'business_permit_renewal'],
    'animal_bite'  => ['table' => 'animal_bite_reports',       'module' => 'animal_bite_reports'],
    'clearance'    => ['table' => 'barangay_clearance',        'module' => 'barangay_clearance'],
    'indigency'    => ['table' => 'indigency',                 'module' => 'indigency']
];

if (!array_key_exists($type, $table_map)) {
    $_SESSION['error_message'] = "Unknown request type: " . htmlspecialchars($type);
    header("Location: ../dashboard.php");
    exit();
}

$table  = $table_map[$type]['table'];
$module = $table_map[$type]['module'];

try {
    // ✅ Delete the pending request
    $sql = "DELETE FROM $table WHERE permit_id = :permit_id AND status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':permit_id' => $permit_id]);

    // ✅ Delete related notifications
    $notif_sql = "DELETE FROM notification WHERE request_id = :permit_id AND module = :module";
    $notif_stmt = $pdo->prepare($notif_sql);
    $notif_stmt->execute([
        ':permit_id' => $permit_id,
        ':module'    => $module
    ]);

    $_SESSION['error_message'] = "❌ Payment was cancelled. Your request has been removed.";
    header("Location: ../dashboard.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    header("Location: ../dashboard.php");
    exit();
}
