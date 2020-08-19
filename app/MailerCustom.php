<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerCustom
{


    /**
     * Method with all what is required to send an email
     *
     * @param type $to The param accepts an array of emails recipients
     * @param type $subject This param is for email subject.. Must be a string only
     */
    public function mailer($to, $subject,  $body)
    {

        $mail = new PHPMailer(true);

       try {
           $mail->Hostname =  env('MAIL_HOST');
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
           $mail->isSMTP();
           $mail->Host       = env('MAIL_HOST');
           $mail->SMTPAuth   = true;
           $mail->Username   = env('MAIL_USERNAME');
           $mail->Password   = env('MAIL_PASSWORD');
           $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
           $mail->Port       = env('MAIL_PORT');

           $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

           foreach ($to as $email) {
               $mail->addAddress($email);
           }

           $mail->addReplyTo(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

           $mail->smtpConnect(
                    array(
                        "ssl" => array(
                            "verify_peer" => false,
                            "verify_peer_name" => false,
                            "allow_self_signed" => true
                    )
                )
            );

           $mail->isHTML(true);
           $mail->Subject = $subject;
           $mail->Body = $body;
           $mail->send();
       } catch (\Throwable $th) {
          $mail->ErrorInfo;
       }
    }
}
