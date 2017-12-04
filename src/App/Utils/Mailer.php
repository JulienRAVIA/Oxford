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

  //   public function __construct()
  //   {
		// self::$mail = new PHPMailer(true);
  //   	//Server settings
  //   	self::$mail->SMTPDebug = 3;                                 // Enable verbose debug output
  //   	self::$mail->isSMTP();                                      // Set mailer to use SMTP
  //   	self::$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
  //   	self::$mail->SMTPAuth = true;                               // Enable SMTP authentication
  //   	self::$mail->Username = 'sio.bonaparte@gmail.com';                 // SMTP username
  //   	self::$mail->Password = 'sCAtenTU';                           // SMTP password
  //   	self::$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
  //   	self::$mail->Port = 587;                                    // TCP port to connect to
  //   	self::$mail->setFrom('sio.bonaparte@gmail.com', 'BTS SIO Lycée Bonaparte');
  //   	return true;
  //   }

    public static function send($address, $subject, $body) {
		  $mail = new PHPMailer(true);
    	//Server settings
    	$mail->SMTPDebug = 0;                                 // Enable verbose debug output
    	$mail->isSMTP();                                      // Set mailer to use SMTP
    	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    	$mail->SMTPAuth = true;                               // Enable SMTP authentication
    	$mail->Username = 'sio.bonaparte@gmail.com';                 // SMTP username
    	$mail->Password = 'sCAtenTU';                           // SMTP password
    	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    	$mail->Port = 587;                                    // TCP port to connect to
    	$mail->setFrom('sio.bonaparte@gmail.com', utf8_decode('BTS SIO Lycée Bonaparte'));
    	$mail->addAddress($address);
    	$mail->Subject = utf8_decode($subject);
    	$mail->Body = utf8_decode($body);
    	try {
    		$mail->send();
    		echo 'Mail envoyé';
    	} catch (Exception $e) {
    		echo 'Mailer Error: ' . $mail->ErrorInfo;
    		throw new \Exception('Envoi impossible');
    	}
    }
}