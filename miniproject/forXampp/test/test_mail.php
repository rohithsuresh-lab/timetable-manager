<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoload

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient = $_POST['email'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mestimtable@gmail.com';         // Your Gmail
        $mail->Password   = 'hvbutvslqvwvxiuy';             // Your Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('mestimtable@gmail.com', 'Dev');
        $mail->addAddress($recipient);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from PHP';
        $mail->Body    = 'This is a test email sent from PHP using PHPMailer.';

        $mail->send();
        $message = "✅ Email sent successfully to $recipient";
    } catch (Exception $e) {
        $message = "❌ Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Test Email</title>
</head>
<body>
    <h2>Send Test Email via PHPMailer</h2>
    <form method="POST">
        <label for="email">Enter recipient email:</label><br>
        <input type="email" name="email" required>
        <br><br>
        <button type="submit">Send Email</button>
    </form>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
</body>
</html>
