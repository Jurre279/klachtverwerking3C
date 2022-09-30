<?php
//Load Composer's autoloader
require '../vendor/autoload.php';

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//monolog
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
include 'prive.php';

try {
    //Server settings
    $mail->SMTPDebug  = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $bedrijfsemail;                     //SMTP username
    $mail->Password   = $wachtwoord;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($bedrijfsemail, 'Klantenserives');
    $mail->addAddress($_POST['Email'], $_POST['Naam']);     //Add a recipient            //Name is optional;
    $mail->addCC($bedrijfsemail);


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Uw klacht is in behandeling';
    $mail->Body    = $_POST['bericht'];

    // logs userdata
    $logger = new Logger('info');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/info.log', Level::Debug));
    $logger->info('user data:', ['name' => $_POST['Naam'], 'emailaddress' => $_POST['Email'], 'description' => $_POST['bericht']]);

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
