<?php

use config\EmailConfiguration;
use helpers\Logger;
use models\Connection;
use models\Log;
use api\exceptions\ApiException;
use helpers\Notifications;
use PHPMailer\PHPMailer\PHPMailer;
use services\SyncService;


include_once 'vendor/autoload.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

/**
 *
 */
abstract class Application
{
    /**
     * @var stdClass|null
     */
    private static ?stdClass $cliArguments = null;

    /**
     *
     */
    public static function main(): void
    {
        try {
            if (php_sapi_name() !== 'cli')
                throw new RuntimeException('This application can only be run from the command line.');

            self::parseCLIArguments();

            self::configureLogging();

            switch (self::$cliArguments->command) {
                case 'cron':
                    self::handleCron();
                    break;
                case 'products':
                    SyncService::init(__DIR__, date('Y-m-d H:i:s'));
                    SyncService::importProductsFromCsvFile();
                    break;
                case 'clients':
                    SyncService::init(__DIR__, date('Y-m-d H:i:s'));
                    SyncService::importCustomersFromCsvFile();
                    break;
                case 'sync':
                    SyncService::init(__DIR__, date('Y-m-d H:i:s'));
                    SyncService::runAllImportProcedures();
                    break;
                case 'smtp':
                    self::handleSMTP();
                    break;
                case 'version':
                    self::displayVersion();
                    break;
                case 'help':
                default:
                    self::displayHelp();
                    break;
            }
        } catch (Exception $e) {
            Logger::log('ERROR', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    private static function parseCLIArguments(): void
    {
        $options = getopt('vh', [
            'cron',
            'smtp',
            'version',
            'help',
            'name:',
            'email:',
            'log-file',
            'products',
            'clients',
            'sync'
        ]);

        self::$cliArguments = new stdClass();

        self::$cliArguments->command = null;
        if (isset($options['cron']))
            self::$cliArguments->command = 'cron';
        if (isset($options['smtp']))
            self::$cliArguments->command = 'smtp';
        if (isset($options['version']) || isset($options['v']))
            self::$cliArguments->command = 'version';
        if (isset($options['help']) || isset($options['h']))
            self::$cliArguments->command = 'help';
        if (isset($options['products']))
            self::$cliArguments->command = 'products';
        if (isset($options['clients']))
            self::$cliArguments->command = 'clients';
        if (isset($options['sync']))
            self::$cliArguments->command = 'sync';

        self::$cliArguments->name = !empty($options['name']) ? $options['name'] : null;
        self::$cliArguments->email = !empty($options['email']) ? $options['email'] : null;
        self::$cliArguments->logFile = isset($options['log-file']);
        self::$cliArguments->clients = isset($options['clients']);
    }

    /**
     * @return void
     */
    private static function configureLogging(): void
    {
        if (!self::$cliArguments->logFile)
            return;
        Logger::configure(__DIR__ . '/.application.log');
    }

    /**
     * @return void
     * @throws ApiException
     */
    private static function handleCron(): void
    {
        $currentTime = date('H:i');
        Logger::log('DEBUG', 'Current time: ' . $currentTime);

        $runTime = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '21:00'];

        if (!in_array($currentTime, $runTime)) {
            Logger::log('DEBUG', 'Time do not match');
            return;
        }

        $updatedAt = date('Y-m-d H:i:s');

        try {
            SyncService::init(__DIR__, $updatedAt);

            Connection::getConn()->begin_transaction();
            SyncService::importCategoriesFromCsvFile();
            Connection::getConn()->commit();

            Connection::getConn()->begin_transaction();
            SyncService::importSubcategoriesFromCsvFile();
            Connection::getConn()->commit();

            Connection::getConn()->begin_transaction();
            SyncService::importProductsFromCsvFile();
            Connection::getConn()->commit();

            SyncService::importCustomersFromCsvFile();

            Connection::getConn()->begin_transaction();
            SyncService::importSellersFromCsvFile();
            Connection::getConn()->commit();

            Connection::getConn()->begin_transaction();
            SyncService::importBalancesFromCsvFile();
            Connection::getConn()->commit();

            Connection::getConn()->begin_transaction();
            $log = new Log();
            $log->setDescription('Actualizacion automatica base de datos');
            $log->setCreatedAt(date('Y-m-d H:i:s'));
            Log::createLog($log);
            Connection::getConn()->commit();

            Logger::log('INFO', 'All sync steps completed successfully');
        } catch (\Throwable $th) {
            Connection::getConn()->rollback();
            $errorMessage = 'Error importing data: ' . $th->getMessage() . "\n" . $th->getTraceAsString();
            Notifications::notifyAdminError('Error en SINCRONIZACIÓN de datos', $errorMessage);
        }
    }

    /**
     * @return void
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private static function handleSMTP(): void
    {
        if (empty(self::$cliArguments->email))
            throw new RuntimeException('Email is required');

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

        $mailer->Subject = 'This is a test';
        $mailer->Body = 'This is a test message';

        $mailer->setFrom(EmailConfiguration::SENDER_EMAIL, EmailConfiguration::SENDER_NAME);
        $mailer->addAddress(self::$cliArguments->email, self::$cliArguments->name ?? '');

        if (!$mailer->send())
            throw new RuntimeException('Mailer Error: ' . $mailer->ErrorInfo);
    }


    /**
     * @return void
     */
    private static function displayVersion(): void
    {
        printf("Version: 1.0.0\n");
    }

    /**
     * @return void
     */
    private static function displayHelp(): void
    {
        if (!file_exists('.help'))
            throw new RuntimeException('Help file does not exist.');
        echo file_get_contents('.help');
    }
}

Application::main();
