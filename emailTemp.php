<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



function welcomeTemp($name)
{

$html = '<!DOCTYPE html>
<html>

<head>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <p style="font-size: medium;font-family: sans-serif;font-weight: 700;">Dear '.$name.',
    </p>
    <p style="font-size: medium;font-family: sans-serif;text-align: justify;width: 92%;">
    
Due to your recent status change with Gurnick Academy, your access to the currently offered courses has been interrupted.
If you feel that this change is an error, please contact your program director or admissions advisor immediately.
Should you choose to re-enroll to Gurnick, you will receive access to new courses upon your return.
For any questions,please contact your Admissions advisor or your Program leadership.

    </p>
</body>
</html>';
return $html;
}


use PHPMailer\PHPMailer\Exception; 
use PHPMailer\PHPMailer\PHPMailer;
require_once 'mailer/vendor/autoload.php';

function sendMail($name)
{

$emailTemp = welcomeTemp($name);
$mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rohit@gurnick.edu';
        $mail->Password = 'Technocodzgroup@123';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->SetFrom('rohit@gurnick.edu','Gurnick Online Administration');

        $mail->addAddress('rahul.singh@technocodz.com');
        $mail->isHTML(true);
        $mail->Subject = 'Your Gurnick Online Access Change';
        $mail->Body = $emailTemp;
        $mail->send();
        $response['success'] = true;
        $response['message'] = 'Email send Successfully';
        echo json_encode($response);
    } catch (Exception $e) {
        $response['error'] = $e;
        $response['message'] = 'Email not send Successfully';
        echo json_encode($response);
    }
}






?>
