<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/OlzMailer.php';

class EmailUtils {
    private $serverConfig;

    public function __construct($serverConfig) {
        $this->serverConfig = $serverConfig;
    }

    public function createEmail() {
        $mail = new OlzMailer(true);

        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = $this->serverConfig->getSmtpHost();
        $mail->SMTPAuth = true;
        $mail->Username = $this->serverConfig->getSmtpUsername();
        $mail->Password = $this->serverConfig->getSmtpPassword();
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = intval($this->serverConfig->getSmtpPort());

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom($this->serverConfig->getSmtpFrom(), 'OL Zimmerberg');

        return $mail;
    }

    public static function fromEnv() {
        global $_CONFIG;
        require_once __DIR__.'/../../config/server.php';

        return new EmailUtils($_CONFIG);
    }
}

function getEmailUtilsFromEnv() {
    // @codeCoverageIgnoreStart
    // Reason: functions cannot be covered.
    return EmailUtils::fromEnv();
    // @codeCoverageIgnoreEnd
}
