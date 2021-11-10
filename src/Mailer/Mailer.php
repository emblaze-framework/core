<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Mailer;

use Emblaze\Bootstrap\App;
use PHPMailer\PHPMailer\PHPMailer;


class Mailer
{
    public function __construct()
    {

    }

    // simple email send message
    public static function sendMsg(
        string $subject = '',
        mixed $body = '',
        mixed $altbody = '',
        string $receiver_email = '',
        string $receiver_name = '',
    )
    {
        
        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            // $mail->isSMTP();                                            //Send using SMTP
            // $mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
            // $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            // $mail->Username   = 'user@example.com';                     //SMTP username
            // $mail->Password   = 'secret';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            // $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            
            extract(App::$app->config['mail']);
            // 'host' => 'smtp.mailtrap.io',
            // 'port' => 25,
            // 'username' => '',
            // 'password' => '',
            // 'smtp_auth' => true,
            // 'smtp_secure' => PHPMailer::ENCRYPTION_STARTTLS,
            // 'set_from' => array('', 'Emblaze'),


            
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $mailers['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailers['smtp']['username'];
            $mail->Password = $mailers['smtp']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $mailers['smtp']['port'];

            // ('emailSender@mail.com','SenderName')
            $mail->setFrom($from['address'], $from['name']);

            //Recipients
            // $mail->setFrom('from@example.com', 'Mailer');
            // $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $mail->addAddress($receiver_email,$receiver_name);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            // $mail->isHTML(true);                                  //Set email format to HTML
            $mail->isHTML(true);                                  
            // $mail->Subject = 'Here is the subject';
            // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altbody;

            $mail->send();
            echo 'Message has been sent';
            
        } catch (\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        
        
    }
}