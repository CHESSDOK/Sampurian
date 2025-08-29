<?php
include '../include/config.php';
include 'auth.php'; // require admin

// only allow these logical types (map to actual table names)
$map = [
    'business_permit' => 'business_permit',
    'business_permit_renewal' => 'business_permit_renewal',
    'barangay_clearance' => 'barangay_clearance',
    'indigency' => 'indigency',
    'animal_bite' => 'animal_bite_reports'
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

// Get user_id from the request before updating
$userSql = "SELECT user_id FROM $table WHERE id = :id";
$userStmt = $pdo->prepare($userSql);
$userStmt->execute([':id' => $id]);
$request = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    http_response_code(404);
    echo "Request not found";
    exit;
}

$user_id = $request['user_id'];

// Only update allowed columns
$sql = "UPDATE $table SET status = :status, comment = :comment WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':status' => $status,
    ':comment' => $comment,
    ':id' => $id
]);

// Create notification for the user
$message = "Your $type request has been $status";
if (!empty($comment)) {
    $message .= ": " . $comment;
}

// Check if notification table exists and has the correct structure
$notificationSql = "INSERT INTO notification (user_id, request_id, message, is_read) VALUES (:user_id, :request_id, :message, 0)";
$notificationStmt = $pdo->prepare($notificationSql);
$notificationStmt->execute([
    ':user_id' => $user_id,
    ':request_id' => $id,
    ':message' => $message
]);

echo "OK";