<?php
session_start();
require_once 'config.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

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
        die("Invalid request type");
    }

    $config = $tables[$request_type];
    $table = $config['table'];
    $id_field = $config['id_field'];
    $purpose_field = $config['purpose_field'];
    $amount = $config['amount'];
    $payment_field = $config['payment_field'];

    // ✅ Fetch transaction data
    $stmt = $pdo->prepare("SELECT r.*, u.f_name, u.m_name, u.l_name 
                           FROM `$table` r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.`$id_field` = ?");
    $stmt->execute([$permit_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        die("Transaction not found");
    }

    // ✅ Build receipt
    $fullName = $transaction['f_name'] . ' ' . $transaction['m_name'] . ' ' . $transaction['l_name'];
    $datePaid = date("F j, Y, g:i a");
    $receiptNumber = "REC-" . strtoupper(bin2hex(random_bytes(3)));
    $purpose = $transaction[$purpose_field] ?? 'N/A';
    $paymentMethod = $transaction[$payment_field] ?? 'Online';

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Payment Receipt - $permit_id</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 40px; 
                line-height: 1.6;
            }
            .header { 
                text-align: center; 
                margin-bottom: 20px; 
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .receipt-info { 
                margin-bottom: 15px; 
            }
            .receipt-info p {
                margin: 8px 0;
            }
            .divider { 
                border-top: 1px solid #ccc; 
                margin: 20px 0; 
            }
            .footer { 
                text-align: center; 
                margin-top: 30px; 
                font-size: 12px; 
                color: #666; 
            }
            .amount {
                font-size: 18px;
                font-weight: bold;
                color: #2c3e50;
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Barangay Official Receipt</h2>
            <h3>Sampurihan Barangay</h3>
        </div>
        
        <div class='receipt-info'>
            <p><strong>Receipt No:</strong> $receiptNumber</p>
            <p><strong>Transaction ID:</strong> $permit_id</p>
            <p><strong>Date:</strong> $datePaid</p>
        </div>
        
        <div class='divider'></div>
        
        <div class='receipt-info'>
            <p><strong>Name:</strong> $fullName</p>
            <p><strong>Service Type:</strong> " . ucfirst($request_type) . "</p>
            <p><strong>Purpose:</strong> $purpose</p>
            <p><strong>Payment Method:</strong> $paymentMethod</p>
        </div>
        
        <div class='divider'></div>
        
        <div class='receipt-info'>
            <p class='amount'><strong>Amount Paid:</strong> Php " . number_format($amount, 2) . "</p>
        </div>
        
        <div class='divider'></div>
        
        <div class='footer'>
            <p>This is an official receipt of payment.</p>
            <p>Thank you for your transaction!</p>
        </div>
    </body>
    </html>";

    try {
        // ✅ Generate PDF and force download
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output as download
        $dompdf->stream("receipt_$permit_id.pdf", [
            "Attachment" => true,
            "compress" => true
        ]);
        exit();
    } catch (Exception $e) {
        die("PDF generation failed: " . $e->getMessage());
    }
}

// If no parameters, show error
echo "Invalid request parameters.";
?>