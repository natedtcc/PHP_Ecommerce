<?php # mailscript.php - N. Nasteff

// This script creates a function that uses PHPMailer to send an activation email to a user
// once they have registered an account. It takes an email address and
// an activation hash as args, then returns a PHPMailer mail object.

// Include PHPMailer lib

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('C:/XAMPP/php/pear/PHPMailer/src/Exception.php');
require('C:/XAMPP/php/pear/PHPMailer/src/PHPMailer.php');
require('C:/XAMPP/php/pear/PHPMailer/src/SMTP.php');


function send_email($address, $mailbody, $subject){


// Create email body - creates a link to activation page
$body = $mailbody;

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Set the hostname of the mail server to gmail..
$mail->Host = 'smtp.gmail.com';

$mail->Port = 587;
//Set the encryption mechanism to use - STARTTLS or SMTPS
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
$mail->SMTPAutoTLS = false;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = '*******';
//Password to use for SMTP authentication
$mail->Password = '*******';
//Set who the message is to be sent from
$mail->setFrom('*******', 'The Jazz Emporium');
//Set an alternative reply-to address
$mail->addReplyTo('*******', 'Nate Nasteff');
//Set who the message is to be sent to
$mail->addAddress($address);
//Set the subject line
$mail->Subject = $subject;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($body);
//Replace the plain text body with one created manually

return $mail;
}