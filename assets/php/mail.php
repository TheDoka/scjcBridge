<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/phpmailer.lang-fr.php';


function sendMail($to, $user, $subject, $body)
{

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'SSL0.OVH.NET';                         // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'marianne-test@scjc-bridge.fr';         // SMTP username
        $mail->Password   = '***********';                          // SMTP password
        $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('marianne-test@scjc-bridge.fr', 'Club de Bridge');
        $mail->addAddress('lucas.ribotto@gmail.com', 'Joe User');     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $body;

        $mail->send();

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

}

    
?>