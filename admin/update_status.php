<?php
include '../include/config.php';
include 'auth.php'; // require admin
include 'generate_pdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

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
$type = $_POST['type'] ?? null;

if (!$id || !$status || !$type || !isset($map[$type])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$table = $map[$type];

switch ($type) {
    case 'business_permit':
        $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                           bp.nature_of_business, bp.kind_of_establishment
                    FROM business_permit bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;

    case 'business_permit_renewal':
        $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                           bp.nature_of_business, bp.name_kind_of_establishment
                    FROM business_permit_renewal bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;

    case 'barangay_clearance':
    case 'indigency':
    case 'animal_bite':
        $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address
                    FROM $table bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;

    default:
        http_response_code(400);
        echo "Invalid request type";
        exit;
}

$userStmt = $pdo->prepare($userSql);
$userStmt->execute([':id' => $id]);
$row = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo "Request not found";
    exit;
}

$user_id = $row['uid'];

// update status
$sql = "UPDATE $table SET status = :status, comment = :comment WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':status' => $status,
    ':comment' => $comment,
    ':id' => $id
]);

// prepare message
$message = "Your $type request has been $status";
if (!empty($comment)) {
    $message .= ": " . $comment;
}

// insert notification
$notificationSql = "INSERT INTO notification (user_id, request_id, message, is_read) 
                    VALUES (:user_id, :request_id, :message, 0)";
$notificationStmt = $pdo->prepare($notificationSql);
$notificationStmt->execute([
    ':user_id' => $user_id,
    ':request_id' => $id,
    ':message' => $message
]);

// --- SEND SMS using ClickSend ---
if (!empty($row['contact'])) {
    $ch = curl_init();

    $smsData = [
        "messages" => [
            [
                "source" => "php",
                "from" => "Barangay",
                "body" => $message,
                "to" => "+63" . ltrim($row['contact'], '0') // convert to PH format
            ]
        ]
    ];

    curl_setopt($ch, CURLOPT_URL, "https://rest.clicksend.com/v3/sms/send");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_USERPWD, "marklawrencemercado8@gmail.com:41C87B9D-0F33-7B69-56A4-EE5A63A4D68C");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($smsData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch);
    if ($output === false) {
        error_log("ClickSend Error: " . curl_error($ch));
    }
    curl_close($ch);
}

// --- SEND EMAIL + PDF if approved ---
if ($status === "Approved") {
    $pdfData = [
        'id' => $id,
        'name' => $row['f_name']." ".$row['m_name']." ".$row['l_name'],
        'address' => $row['address'],
        'age' => $row['age'] ?? '___', // for indigency
        'nature_of_business' => $row['nature_of_business'] ?? '',
        'kind_of_establishment' => $row['kind_of_establishment'] ?? '',
        'incident_details' => $row['incident_details'] ?? '' // for animal bite
    ];

    $attachment = generatePDF($type, $pdfData);

    // save path to DB
    $updatePdf = $pdo->prepare("UPDATE $table SET pdf_path = :pdf WHERE id = :id");
    $updatePdf->execute([
        ':pdf' => $attachment,
        ':id' => $id
    ]);

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ict1mercado.cdlb@gmail.com';
        $mail->Password   = 'swnr plwx zscz yxce'; // or App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('ict1mercado.cdlb@gmail.com', 'Barangay Office');
        $mail->addAddress($row['email'], $row['f_name'] . " " . $row['l_name']);

        $mail->isHTML(true);
        $mail->Subject = "Your $type Request Status";
        $mail->Body    = nl2br($message);

        if (file_exists($attachment)) {
            $mail->addAttachment($attachment);
        }

        $mail->send();
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
    }
}

echo "OK";
