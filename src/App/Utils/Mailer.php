<?php 

namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe utilitaire d'envoi de mail
 */
class Mailer
{
	private static $instance;
	private $mail;

    public static function send($address, $subject, $body) {
		$mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'sio.bonaparte@gmail.com';                 // SMTP username
        $mail->Password = 'uwannaknowright?';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        $mail->setFrom('sio.bonaparte@gmail.com', utf8_decode('BTS SIO Lycée Bonaparte'));
        $mail->addAddress($address);
        $mail->isHTML(true);     
        $mail->Subject = utf8_decode($subject);
        $mail->Body = utf8_decode($body);
        try {
    	  	$mail->send();
    	} catch (Exception $e) {
    		echo 'Mailer Error: ' . $mail->ErrorInfo;
    		throw new \Exception('Envoi impossible');
    	}
    }
}