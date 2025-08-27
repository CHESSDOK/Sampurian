<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require '../vendor/autoload.php';

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pesolosbanos4@gmail.com';  // Replace with Barangay Gmail
        $mail->Password   = 'rooy awbq emme qqyt';    // Use Gmail App Password, not normal password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($email, $name); 
        $mail->addAddress('mercadomarklawrence55@gmail.com', 'Barangay Sampiruhan'); 

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Inquiry from Barangay Sampiruhan Website';
        $mail->Body    = "
            <h3>New Inquiry Received</h3>
            <p><b>Name:</b> {$name}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Contact:</b> {$contact}</p>
            <p><b>Message:</b><br>{$message}</p>
        ";

        $mail->send();
        echo "<script>alert('Your inquiry has been sent successfully!'); window.location.href='../dashboard.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.location.href='../dashboard.php';</script>";
    }
} else {
    header("Location: dashboard.php");
    exit();
}
