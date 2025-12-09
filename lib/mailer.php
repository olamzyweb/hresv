<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_email(string $to, string $subject, string $html, string $text = ''): bool
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST') ?: 'localhost';
        $mail->Port = (int)(getenv('MAIL_PORT') ?: 1025);
        if ($user = getenv('MAIL_USER')) {
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = getenv('MAIL_PASS') ?: '';
        }
        $mail->setFrom(getenv('MAIL_FROM') ?: 'hotel@example.com', getenv('MAIL_FROM_NAME') ?: 'HRest Hotel');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = $text ?: strip_tags($html);
        return $mail->send();
    } catch (Exception $e) {
        log_message('mail_error', $e->getMessage());
        return false;
    }
}

