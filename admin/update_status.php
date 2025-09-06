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

$id     = $_POST['id']     ?? null;
$status = $_POST['status'] ?? null;
$comment= $_POST['comment']?? null;
$type   = $_POST['type']   ?? null;

if (!$id || !$status || !$type || !isset($map[$type])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$table = $map[$type];

// pull user info (extend if you need extra fields for PDFs)
switch ($type) {
    case 'business_permit':
        $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                           u.birthday, u.picture,
                           bp.nature_of_business, bp.kind_of_establishment
                    FROM business_permit bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;

    case 'business_permit_renewal':
        $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                           u.birthday, u.picture,
                           bp.nature_of_business, bp.name_kind_of_establishment AS kind_of_establishment
                    FROM business_permit_renewal bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;

    case 'barangay_clearance':
         $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                           u.birthday, u.picture, bp.years_stay_in_barangay, bp.purpose
                    FROM barangay_clearance bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;
    case 'indigency':
        $userSql = "SELECT u.id as uid, u.f_name, u.m_name, u.l_name, u.email, u.contact, u.address,
                           u.birthday, u.picture
                    FROM indigency bp
                    JOIN users u ON u.id = bp.user_id
                    WHERE bp.id = :id";
        break;

case 'animal_bite':
    $userSql = "SELECT 
                    u.id AS uid,
                    u.email,
                    u.f_name,
                    u.m_name,
                    u.l_name,
                    bp.last_name,
                    bp.first_name,
                    bp.middle_name,
                    bp.dob,
                    bp.age,
                    bp.gender,
                    bp.contact,
                    bp.guardian,
                    bp.bite_location,
                    bp.body_part,
                    bp.washed,
                    bp.bite_date,
                    bp.animal_description,
                    bp.color,
                    bp.marks,
                    bp.animal_condition,
                    bp.registered,
                    bp.other_animals,
                    bp.dog_condition,
                    bp.owner_name
                FROM animal_bite_reports bp
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

$user_id = (int)$row['uid'];

// update status + comment
$sql = "UPDATE $table SET status = :status, comment = :comment WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':status' => $status,
    ':comment' => $comment,
    ':id' => $id
]);

// prepare message used for email/SMS (you said message column isn't needed for UI)
$message = "Your $type request has been $status";
if (!empty($comment)) {
    $message .= ": " . $comment;
}

// 1) Insert USER notification for the status update
$ins = $pdo->prepare("
    INSERT INTO notification (user_id, request_id, module, recipient_type, is_read, is_read_admin)
    VALUES (:user_id, :request_id, :module, 'user', 0, 1)
");
$ins->execute([
    ':user_id'    => $user_id,
    ':request_id' => $id,
    ':module'     => $type
]);

// 2) Mark the corresponding ADMIN notification (for this request) as read (if it exists)
$updAdmin = $pdo->prepare("
    UPDATE notification 
    SET is_read_admin = 1 
    WHERE recipient_type='admin' 
      AND module = :module 
      AND request_id = :request_id
      AND is_read_admin = 0
");
$updAdmin->execute([
    ':module' => $type,
    ':request_id' => $id
]);

// --- SEND SMS using ClickSend (as in your code) ---
if (!empty($row['contact'])) {
    $ch = curl_init();

    $smsData = [
        "messages" => [
            [
                "source" => "php",
                "from" => "Barangay",
                "body" => $message,
                "to" => "+63" . ltrim($row['contact'], '0')
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
    if ($type === "animal_bite") {
        // ---- Animal Bite style names ----
        $middleInitial = '';
        if (!empty($row['middle_name'])) {
            $middleInitial = strtoupper(substr($row['middle_name'], 0, 1)) . ".";
        }

        $fullName  = trim($row['first_name'] . " " . $middleInitial . " " . $row['last_name']);
        $fullNameA = trim($row['last_name'] . ", " . $row['first_name'] . " " . $row['middle_name']);
    } else {
        // ---- All other requests ----
        $middleInitial = '';
        if (!empty($row['m_name'])) {
            $middleInitial = strtoupper(substr($row['m_name'], 0, 1)) . ".";
        }

        $fullName  = trim($row['f_name'] . " " . $middleInitial . " " . $row['l_name']);
        $fullNameA = ''; // not used
    }

    $pdfData = [
        'id' => $id,
        'name' => $fullName,          // for indigency, clearance, business permits
        'full_nameA' => $fullNameA,   // for animal bite only

        // shared
        'address' => $row['address'] ?? '',
        'birthday' => $row['birthday'] ?? ($row['dob'] ?? ''),
        'picture' => !empty($row['picture']) 
            ? ("../include/".$row['picture']) 
            : '../user-placeholder.png',

        // business
        'nature_of_business' => $row['nature_of_business'] ?? '',
        'kind_of_establishment' => $row['kind_of_establishment'] ?? '',

        // clearance/indigency
        'years_stay_in_barangay' => $row['years_stay_in_barangay'] ?? '',
        'purpose' => $row['purpose'] ?? '', 

        // animal bite
        'dob'               => $row['dob'] ?? '',
        'age'               => $row['age'] ?? '',
        'gender'            => $row['gender'] ?? '',
        'contact'           => $row['contact'] ?? '',
        'guardian'          => $row['guardian'] ?? '',
        'bite_location'     => $row['bite_location'] ?? '',
        'body_part'         => $row['body_part'] ?? '',
        'washed'            => $row['washed'] ?? '',
        'bite_date'         => $row['bite_date'] ?? '',
        'animal_description'=> $row['animal_description'] ?? '',
        'color'             => $row['color'] ?? '',
        'marks'             => $row['marks'] ?? '',
        'animal_condition'  => $row['animal_condition'] ?? '',
        'registered'        => $row['registered'] ?? '',
        'other_animals'     => $row['other_animals'] ?? '',
        'dog_condition'     => $row['dog_condition'] ?? '',
        'owner_name'        => $row['owner_name'] ?? ''
    ];


    $attachment = generatePDF($type, $pdfData);

    // save pdf path
    $updatePdf = $pdo->prepare("UPDATE $table SET pdf_path = :pdf WHERE id = :id");
    $updatePdf->execute([
        ':pdf' => $attachment,
        ':id' => $id
    ]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ict1mercado.cdlb@gmail.com';
        $mail->Password   = 'swnr plwx zscz yxce'; // app password
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
