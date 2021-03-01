<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__.'/../../config/vendor/autoload.php';

class OlzMailer extends PHPMailer {
    private $emailUtils;
    private $serverConfig;

    public function __construct($emailUtils, $serverConfig, $enable_exceptions) {
        parent::__construct($enable_exceptions);
        $this->emailUtils = $emailUtils;
        $this->serverConfig = $serverConfig;
    }

    public function configure($user, $title, $text) {
        $user_email = $user->getEmail();
        // TODO: Check if verified?
        $user_id = $user->getId();
        $user_full_name = $user->getFullName();
        $user_first_name = $user->getFirstName();
        $this->addAddress($user_email, $user_full_name);
        $html_text = nl2br($text);
        $this->isHTML(true);
        $this->AddEmbeddedImage(__DIR__.'/../../icns/olz_logo_schwarzweiss_300.png', 'olz_logo');
        $unsubscribe_this_token = urlencode($this->emailUtils->encryptEmailReactionToken([
            'action' => 'unsubscribe',
            'user' => $user_id,
            'notification_type' => 'monthly_preview', // TODO: correct notification type
        ]));
        $unsubscribe_all_token = urlencode($this->emailUtils->encryptEmailReactionToken([
            'action' => 'unsubscribe',
            'user' => $user_id,
            'notification_type_all' => true,
        ]));
        $base_url = $this->serverConfig->getBaseHref();
        $unsubscribe_this_url = "{$base_url}/email_reaktion.php?token={$unsubscribe_this_token}";
        $unsubscribe_all_url = "{$base_url}/email_reaktion.php?token={$unsubscribe_all_token}";
        $this->Subject = "[OLZ] {$title}";
        $this->Body = <<<ZZZZZZZZZZ
        <div style="text-align: right; float: right;">
            <img src="cid:olz_logo" alt="" style="width:150px;" />
        </div>
        Hallo <b>{$user_first_name}</b>,<br />
        {$html_text}<br />
        <br />
        <hr style="border: 0; border-top: 1px solid black;">
        Abmelden? <a href="{$unsubscribe_this_url}">Keine solchen E-Mails mehr</a> - <a href="{$unsubscribe_all_url}">Keine E-Mails von OL Zimmerberg mehr</a>
        ZZZZZZZZZZ;
        $this->AltBody = <<<ZZZZZZZZZZ
        Hallo {$user_first_name},

        {$text}

        ---
        Abmelden?
        Keine solchen E-Mails mehr: {$unsubscribe_this_url}
        Keine E-Mails von OL Zimmerberg mehr: {$unsubscribe_all_url}
        ZZZZZZZZZZ;
    }

    public function send() {
        try {
            parent::send();
        } catch (Exception $e) {
            // TODO: Logging
            // $this->logger->critical("{$mail->ErrorInfo}");
            throw $e;
        }
        // @codeCoverageIgnoreStart
        // Reason: Email cannot be sent in tests.
    }

    // @codeCoverageIgnoreEnd
}
