<?php

namespace Local\Core\Inner;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

/**
 * Класс для работы с почтой
 *
 * Class Mail
 * @package Local\Core\Inner
 */
class Mail
{
    public static function send($to, $subject, $message, $additional_headers = "", $additional_parameters = "", \Bitrix\Main\Mail\Context $context = null)
    {
        $arMailConfig = \Bitrix\Main\Config\Configuration::getInstance()
            ->get('mail')['smtp'];

        $mail = new PHPMailer(true);
        try {

            //парсим дополнительные заголовки в массив
            $arHeaders = [];
            if (!empty($additional_headers)) {
                $explode = explode("\n", $additional_headers);
                foreach ($explode as $strHeader) {
                    if (preg_match('/^([^\:]+)\:(.*)$/', $strHeader, $matches)) {
                        $key = trim($matches[1]);
                        $value = trim($matches[2]);
                        $arHeaders[$key] = $value;
                    }
                }
            }

            if (function_exists('mb_internal_encoding') && ((int)ini_get('mbstring.func_overload')) & 2) {
                mb_internal_encoding('ASCII');
            }

            //Server settings
            $mail->Timeout = 3;
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->CharSet = 'UTF-8';
            $mail->setLanguage('ru');
            $mail->Host = $arMailConfig['host'];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $arMailConfig['login'];                 // SMTP username
            $mail->Password = $arMailConfig['password'];                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($arMailConfig['login'], $arMailConfig['name']);
            $mail->addAddress($to);

            //Content
            $mail->Subject = $subject;
            foreach (array_map('trim', explode(',', $to)) as $emailTo) {
                $mail->addAddress($emailTo);
            }
            //парсим копии, если есть
            if (!empty($arHeaders['CC'])) {
                foreach (array_map('trim', explode(',', $arHeaders['CC'])) as $emailTo) {
                    $mail->addCC($emailTo);
                }
                unset($arHeaders['CC']);
            }
            //парсим скрытые копии, если есть
            if (!empty($arHeaders['BCC'])) {
                foreach (array_map('trim', explode(',', $arHeaders['BCC'])) as $emailTo) {
                    $mail->addBCC($emailTo);
                }
                unset($arHeaders['BCC']);
            }

            $mail->Subject = $subject;


            $mail->isHTML(true);
            $mail->Body = $message;
            $mail->AltBody = $message;


            return $mail->send();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}