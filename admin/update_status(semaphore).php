<?php
include '../include/config.php';
include 'auth.php'; // require admin

// load helpers
include 'generate_pdf.php';
require '../vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Map request types to tables
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

// fetch request & user details
switch ($type) {
    case 'business_permit':
        $sql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                       bp.kind_of_establishment, bp.nature_of_business
                FROM business_permit bp
                JOIN users u ON u.id = bp.user_id
                WHERE bp.id = :id";
        break;

    case 'business_permit_renewal':
        $sql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                       br.name_kind_of_establishment, br.nature_of_business
                FROM business_permit_renewal br
                JOIN users u ON u.id = br.user_id
                WHERE br.id = :id";
        break;

    case 'barangay_clearance':
        $sql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                       bc.years_stay_in_barangay, bc.purpose
                FROM barangay_clearance bc
                JOIN users u ON u.id = bc.user_id
                WHERE bc.id = :id";
        break;

    case 'indigency':
        $sql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                       i.nature_of_assistance
                FROM indigency i
                JOIN users u ON u.id = i.user_id
                WHERE i.id = :id";
        break;

    case 'animal_bite':
        $sql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                       ab.bite_location, ab.animal_description, ab.color
                FROM animal_bite_reports ab
                JOIN users u ON u.id = ab.user_id
                WHERE ab.id = :id";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo "Request not found";
    exit;
}

$user_id = $row['uid'];

// update status
$update = $pdo->prepare("UPDATE $table SET status = :status, comment = :comment WHERE id = :id");
$update->execute([
    ':status' => $status,
    ':comment' => $comment,
    ':id' => $id
]);

// prepare message
$message = "Your $type request has been $status.";
if (!empty($comment)) {
    $message .= " Note: " . $comment;
}

// insert notification
$notify = $pdo->prepare("INSERT INTO notification (user_id, request_id, message, is_read) 
                         VALUES (:user_id, :request_id, :message, 0)");
$notify->execute([
    ':user_id' => $user_id,
    ':request_id' => $id,
    ':message' => $message
]);

// --- SEND SMS (Semaphore Example) ---
if (!empty($row['contact'])) {
    $ch = curl_init();
    $smsData = [
        'apikey' => 'd849e46c626763ce8c95acbfb93426f5',
        'number' => $row['contact'],
        'message' => $message,
        'sendername' => 'SEMAPHORE'
    ];
    curl_setopt($ch, CURLOPT_URL, "https://semaphore.co/api/v4/messages");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($smsData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
}

// --- SEND EMAIL + PDF if approved ---
if ($status === "Approved") {
    // prepare PDF data
    $pdfData = [
        'id' => $id,
        'name' => $row['f_name']." ".$row['m_name']." ".$row['l_name'],
        'address' => $row['address'],
        'age' => $row['age'] ?? '',
        'nature_of_business' => $row['nature_of_business'] ?? '',
        'kind_of_establishment' => $row['kind_of_establishment'] ?? ($row['name_kind_of_establishment'] ?? ''),
        'incident_details' => $row['bite_location'] ?? ''
    ];

    $attachment = generatePDF($type, $pdfData);

    // save pdf path
    $updatePdf = $pdo->prepare("UPDATE $table SET pdf_path = :pdf WHERE id = :id");
    $updatePdf->execute([
        ':pdf' => $attachment,
        ':id' => $id
    ]);

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // change to your SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'pesolosbanos4@gmail.com';
        $mail->Password = 'rooy awbq emme qqyt'; // Use App Password for Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('pesolosbanos4@gmail.com', 'Barangay System');
        $mail->addAddress($row['email'], $row['f_name']." ".$row['l_name']);
        $mail->addAttachment($attachment);

        $mail->isHTML(true);
        $mail->Subject = "Your $type request has been approved";
        $mail->Body = "<p>Good day,</p><p>Your <b>$type</b> request has been approved.</p><p>Please see attached document.</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}

echo "OK";
