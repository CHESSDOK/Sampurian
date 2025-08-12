<?php
include '../include/config.php';
include 'auth.php'; // require admin

// only allow these logical types (map to actual table names)
$map = [
    'business_permit' => 'business_permit',
    'business_permit_renewal' => 'business_permit_renewal',
    'barangay_clearance' => 'barangay_clearance',
    'indigency' => 'indigency',
    'animal_bite' => 'animal_bite_investigation_report'
];

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;
$comment = $_POST['comment'] ?? null;
$type = $_POST['type'] ?? null; // logical type key from map

if (!$id || !$status || !$type || !isset($map[$type])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$table = $map[$type];

// Only update allowed columns
$sql = "UPDATE $table SET status = :status, comment = :comment WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':status' => $status,
    ':comment' => $comment,
    ':id' => $id
]);

echo "OK";
