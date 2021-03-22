<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__.'/../../config/vendor/autoload.php';

class OlzMailer extends PHPMailer {
    use Psr\Log\LoggerAwareTrait;

    private $emailUtils;
    private $envUtils;

    public function __construct($emailUtils, $envUtils, $enable_exceptions) {
        parent::__construct($enable_exceptions);
        $this->emailUtils = $emailUtils;
        $this->envUtils = $envUtils;
    }

    public function configure($user, $title, $text, $config = []) {
        $user_email = $user->getEmail();
        // TODO: Check if verified?
        $user_id = $user->getId();
        $user_full_name = $user->getFullName();
        $user_first_name = $user->getFirstName();
        $this->addAddress($user_email, $user_full_name);
        $html_text = $this->emailUtils->renderMarkdown($text);
        $this->isHTML(true);
        $this->AddEmbeddedImage(__DIR__.'/../../icns/olz_logo_schwarzweiss_300.png', 'olz_logo');
        if (!isset($config['notification_type'])) {
            $this->logger->warning("E-Mail has no notification_type (to user: {$user_id}): {$html_text}");
        }
        $unsubscribe_this_token = urlencode($this->emailUtils->encryptEmailReactionToken([
            'action' => 'unsubscribe',
            'user' => $user_id,
            'notification_type' => $config['notification_type'] ?? null,
        ]));
        $unsubscribe_all_token = urlencode($this->emailUtils->encryptEmailReactionToken([
            'action' => 'unsubscribe',
            'user' => $user_id,
            'notification_type_all' => true,
        ]));
        $base_url = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();
        $unsubscribe_this_url = "{$base_url}{$code_href}email_reaktion.php?token={$unsubscribe_this_token}";
        $unsubscribe_all_url = "{$base_url}{$code_href}email_reaktion.php?token={$unsubscribe_all_token}";
        $this->Subject = "[OLZ] {$title}";
        $this->Body = <<<ZZZZZZZZZZ
        <div style="text-align: right; float: right;">
            <img src="cid:olz_logo" alt="" style="width:150px;" />
        </div>
        <br /><br /><br />
        {$html_text}<br />
        <br />
        <hr style="border: 0; border-top: 1px solid black;">
        Abmelden? <a href="{$unsubscribe_this_url}">Keine solchen E-Mails mehr</a> oder <a href="{$unsubscribe_all_url}">Keine E-Mails von OL Zimmerberg mehr</a>
        ZZZZZZZZZZ;
        $this->AltBody = <<<ZZZZZZZZZZ
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
