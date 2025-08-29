<?php
session_start();
require_once 'config.php';
require '../vendor/autoload.php'; // Dompdf

use Dompdf\Dompdf;

$permit_id = $_GET['permit_id'] ?? null;

if ($permit_id) {
    // ✅ Mark payment as paid
    $stmt = $pdo->prepare("UPDATE indigency SET status = 'paid' WHERE permit_id = ?");
    $stmt->execute([$permit_id]);

    // ✅ Fetch user & transaction details
    $stmt = $pdo->prepare("SELECT i.*, u.f_name, u.m_name, u.l_name 
                           FROM indigency i 
                           JOIN users u ON i.user_id = u.id 
                           WHERE i.permit_id = ?");
    $stmt->execute([$permit_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        // ✅ Create receipt HTML
        $fullName = $transaction['f_name'] . ' ' . $transaction['m_name'] . ' ' . $transaction['l_name'];
        $datePaid = date("F j, Y, g:i a");

        $receiptNumber = "REC-" . strtoupper(bin2hex(random_bytes(3)));

        $html = "
        <h2 style='text-align:center;'>Barangay Payment Receipt</h2>
        <hr>
        <p><strong>Receipt No:</strong> {$receiptNumber}</p>
        <p><strong>Permit ID:</strong> {$transaction['permit_id']}</p>
        <p><strong>Name:</strong> {$fullName}</p>
        <p><strong>Purpose:</strong> {$transaction['nature_of_assistance']}</p>
        <p><strong>Amount Paid:</strong> Php 500.00</p>
        <p><strong>Payment Method:</strong> {$transaction['payment_type']}</p>
        <p><strong>Date Paid:</strong> {$datePaid}</p>
        <hr>
        <p style='text-align:center;'>This is an official receipt of payment.</p>
        ";

        // ✅ Generate PDF using Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // ✅ Save PDF to server (optional: store path in DB)
        $receiptPath = "../receipts/receipt_{$permit_id}.pdf";
        file_put_contents($receiptPath, $dompdf->output());

        // ✅ Trigger download for user
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=receipt_$permit_id.pdf");
        echo $dompdf->output();

        // ✅ Redirect to dashboard after download
        echo "
            <script>
                setTimeout(function(){
                    window.location.href = '../dashboard.php';
                }, 3000);
            </script>
        ";
        exit();
    }
}

// If no transaction found
$_SESSION['error_message'] = "❌ Unable to generate receipt.";
header("Location: ../dashboard.php");
exit();
