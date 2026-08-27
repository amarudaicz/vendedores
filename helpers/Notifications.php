<?php

namespace helpers;

use config\EmailConfiguration;
use config\SiteConfiguration;
use models\Order;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 *
 */
abstract class Notifications {
    /**
     * @param Order  $order
     * @param string $filename
     * @param string $customerName
     *
     * @return void
     * @throws Exception
     */
    public static function sendCustomerOrder(Order $order, string $filename, string $customerName): void {
        $mailer = new PHPMailer(true);
        $mailer->Host = EmailConfiguration::SMTP_HOST;
        $mailer->Port = EmailConfiguration::SMTP_PORT;
        $mailer->Username = EmailConfiguration::SMTP_USERNAME;
        $mailer->Password = EmailConfiguration::SMTP_PASSWORD;
        $mailer->SMTPAuth = true;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->isSMTP();
        $mailer->isHTML();

        $mailer->Subject = sprintf('Orden de compra #%08d', $order->getId());
        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);
        $mailer->addAddress(EmailConfiguration::VENTAS_EMAIL, EmailConfiguration::VENTAS_NAME);
        $mailer->addAttachment($filename);

        $html = file_get_contents('public/templates/customer-order-email.html');

        $html = str_replace(['#CUSTOMER_NAME#'], $customerName, $html);

        $mailer->Body = $html;

        if (!$mailer->send())
            throw new Exception('El email no se envió', 500);
    }

    /**
     * @param Order  $order
     * @param string $filename
     * @param array  $data
     *
     * @return void
     * @throws Exception
     */
    public static function sendGuestOrder(Order $order, string $filename, array $data): void {
        $mailer = new PHPMailer(true);
        $mailer->Host = EmailConfiguration::SMTP_HOST;
        $mailer->Port = EmailConfiguration::SMTP_PORT;
        $mailer->Username = EmailConfiguration::SMTP_USERNAME;
        $mailer->Password = EmailConfiguration::SMTP_PASSWORD;
        $mailer->SMTPAuth = true;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->isSMTP();
        $mailer->isHTML();

        $mailer->Subject = sprintf('Orden de compra #%08d', $order->getId());
        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);
        $mailer->addAddress(EmailConfiguration::VENTAS_EMAIL, EmailConfiguration::VENTAS_NAME);
        $mailer->addAttachment($filename);

        $html = file_get_contents('public/templates/guest-order-email.html');

        $html = str_replace(['#NAME#', '#PHONE#', '#CUIT#', '#LOCATION#', '#POSTAL_CODE#'], $data, $html);

        $mailer->Body = $html;

        if (!$mailer->send())
            throw new Exception('El email no se envió', 500);
    }

    public static function sendSellerOrder(Order $order, string $filename, array $data): void {
        $mailer = new PHPMailer(true);
        $mailer->Host = EmailConfiguration::SMTP_HOST;
        $mailer->Port = EmailConfiguration::SMTP_PORT;
        $mailer->Username = EmailConfiguration::SMTP_USERNAME;
        $mailer->Password = EmailConfiguration::SMTP_PASSWORD;
        $mailer->SMTPAuth = true;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->isSMTP();
        $mailer->isHTML();

        $mailer->Subject = sprintf('Orden de compra #%08d', $order->getId());
        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);
        $mailer->addAttachment($filename);
        
        $html = file_get_contents('public/templates/seller-order-email.html');

        $html = str_replace(['#CUSTOMER_NAME#'], $data, $html);

        $mailer->Body = $html;

        if (!$mailer->send())
            throw new Exception('El email no se envió', 500);
    }

    /**
     * Envía al cliente/invitado el mail con el nuevo estado de su pedido.
     * Opcionalmente adjunta el PDF del pedido (p. ej. cuando pasa a CONFIRMADO).
     *
     * @param Order       $order
     * @param string      $toEmail
     * @param string      $statusLabel
     * @param string      $customerName
     * @param string|null $pdfPath
     * @return void
     * @throws Exception
     */
    public static function sendOrderStatusUpdate(Order $order, string $toEmail, string $statusLabel, string $customerName, ?string $pdfPath = null): void {
        $mailer = new PHPMailer(true);
        $mailer->Host = EmailConfiguration::SMTP_HOST;
        $mailer->Port = EmailConfiguration::SMTP_PORT;
        $mailer->Username = EmailConfiguration::SMTP_USERNAME;
        $mailer->Password = EmailConfiguration::SMTP_PASSWORD;
        $mailer->SMTPAuth = true;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->isSMTP();
        $mailer->isHTML();

        $mailer->Subject = sprintf('Pedido web #%08d %s - %s', $order->getId(), $statusLabel, SiteConfiguration::NAME);
        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);
        $mailer->addReplyTo('ventas@nvd.com.ar', 'Ventas NVD');
        $mailer->addAddress($toEmail, $customerName);

        if ($pdfPath !== null && file_exists($pdfPath)) {
            $mailer->addAttachment($pdfPath);
        }

        $footerParts = array_filter([
            SiteConfiguration::NAME,
            EmailConfiguration::VENTAS_EMAIL,
            SiteConfiguration::PHONE,
        ]);

        $footer = implode(' &nbsp;|&nbsp; ', array_map('htmlspecialchars', $footerParts));

        $html = file_get_contents('public/templates/order-status-email.html');

        $html = str_replace(
            ['#ORDER_ID#', '#ORDER_STATUS#', '#CUSTOMER_NAME#', '#FOOTER#'],
            [sprintf('%08d', $order->getId()), htmlspecialchars($statusLabel), htmlspecialchars($customerName), $footer],
            $html
        );

        $mailer->Body = $html;

        if (!$mailer->send())
            throw new Exception('El email no se envió', 500);
    }

    public static function notifyAdminError(string $subject, string $message): void
{
    $mailer = new PHPMailer(true);
    try {
        $mailer->Host = EmailConfiguration::SMTP_HOST;
        $mailer->Port = EmailConfiguration::SMTP_PORT;
        $mailer->Username = EmailConfiguration::SMTP_USERNAME;
        $mailer->Password = EmailConfiguration::SMTP_PASSWORD;
        $mailer->SMTPAuth = true;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->isSMTP();
        $mailer->isHTML(true);

        $mailer->Subject = $subject;
        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);
        $mailer->addAddress('amarudaicz10@gmail.com', 'Administrador');
        $mailer->addAddress('quanticasoft@gmail.com', 'Administrador');
        $mailer->addAddress('info@wizdigitalgroup.com', 'Administrador');
        $mailer->addAddress('team.wizds@gmail.com', 'Administrador');

        $mailer->Body = '<h3>Se produjo un error en el sistema</h3><p>' . nl2br(htmlentities($message)) . '</p>';

        $mailer->send();
    } catch (Exception $e) {
        Logger::log('ERROR', 'No se pudo enviar el correo de error: ' . $mailer->ErrorInfo);
    }
}


    
}