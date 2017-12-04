<?php 

require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\Utils\Mailer;
use App\View;

// Mailer::send('jrgfawkes@gmail.com', 'TEST', 'TSEFEF');
$mail = new PHPMailer(true);
    	//Server settings
$mail->SMTPDebug = 0;                                 // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'sio.rgvrg@gmail.com';                 // SMTP username
$mail->Password = 'sCAtenTU';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to
$mail->setFrom('sio.bonaparte@gmail.com', utf8_decode('BTS SIO Lycée Bonaparte'));
$mail->addAddress('jrgfawkes@gmail.com');
$mail->Subject = utf8_decode('FGRGG');
$mail->Body = utf8_decode('JIEIGK?RG');
$mail->send();
