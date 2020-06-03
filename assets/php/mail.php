<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/phpmailer.lang-fr.php';


function sendMail($mailContent)
{

    /*
    
    Array
    (
        [0] => Array
            (
                [subject] => 
                [body] =>  
                [to] => Array
                    (
                        [0] => 
                        [1] => 
                        [2] => 
                    )

            )

    )

    */
    
    // Instantiation and passing `true` enables exceptions
    for ($i=0; $i < sizeof($mailContent[0]['to']); $i++) { 
    
        $mail = new PHPMailer(true);

        try {

            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'SSL0.OVH.NET';                         // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'marianne-test@scjc-bridge.fr';         // SMTP username
            $mail->Password   = '';                          // SMTP password
            $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('marianne-test@scjc-bridge.fr', 'Club de Bridge');
            $mail->addAddress($mailContent[0]['to'][$i], $mailContent[0]['to'][$i]);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $mailContent[0]['subject'];
            $mail->Body    = $mailContent[0]['body'];
            $mail->AltBody = $mailContent[0]['body'];

            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
      
    }    

}

    
?>