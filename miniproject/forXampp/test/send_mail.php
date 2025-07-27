<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php'; // Adjust path as needed

function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mestimtable@gmail.com';       // Your Gmail
        $mail->Password   = 'hvbutvslqvwvxiuy';             // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('mestimtable@gmail.com', 'Dev');
        $mail->addAddress($recipient);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return "✅ Email sent successfully to $recipient";
    } catch (Exception $e) {
        return "❌ Error: {$mail->ErrorInfo}";
    }
}
