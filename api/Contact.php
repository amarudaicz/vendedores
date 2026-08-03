<?php

namespace api;

use JsonException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use helpers\Request;
use helpers\Response;
use helpers\GoogleRecaptcha;
use config\EmailConfiguration;
use api\exceptions\ApiException;

/**
 *
 */
abstract class Contact {
    /**
     * @return PHPMailer
     * @throws Exception
     */
    private static function getMailer(): PHPMailer {
        $mailer = new PHPMailer(false);

        $mailer->Host = EmailConfiguration::SMTP_HOST;
        $mailer->Port = EmailConfiguration::SMTP_PORT;
        $mailer->Username = EmailConfiguration::SMTP_USERNAME;
        $mailer->Password = EmailConfiguration::SMTP_PASSWORD;
        $mailer->SMTPAuth = true;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->isSMTP();
        $mailer->isHTML();

        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);

        return $mailer;
    }

    /**
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public static function sendContact(): void {
        $requestData = Request::getJson();

        if (empty($requestData->name))
            throw new ApiException('El nombre es requerido', 400);

        if (empty($requestData->email))
            throw new ApiException('El email es requerido', 400);

        if (!filter_var($requestData->email, FILTER_VALIDATE_EMAIL))
            throw new ApiException('El email es invalido', 400);

        if (empty($requestData->phone))
            throw new ApiException('El teléfono celular es requerido', 400);

        if (empty($requestData->message))
            throw new ApiException('El mensaje es requerido', 400);

        if (empty($requestData->token))
            throw new ApiException('El token de recaptcha es requerido', 400);

        GoogleRecaptcha::validate($requestData->token, 'contact');

        $mailer = self::getMailer();

        $mailer->Subject = 'Contacto desde essencedubai.com.ar';

        $mailer->addAddress('contacto@essencedubai.com.ar', 'Contacto Essence Dubai');

        $mailer->addReplyTo($requestData->email, $requestData->name);

        $emailTemplate = file_get_contents('public/templates/contact-email.html');

        $mailer->Body = str_replace([
            '#NAME#',
            '#EMAIL#',
            '#PHONE#',
            '#MESSAGE#',
        ], [
            $requestData->name,
            $requestData->email,
            $requestData->phone,
            $requestData->message
        ], $emailTemplate);

        if (!$mailer->send())
            throw new ApiException('Hubo un error al intentar enviar el email. Intente nuevamente mas tarde', 500);

        Response::setCode(204);
    }
}