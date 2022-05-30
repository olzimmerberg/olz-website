<?php

use PHPMailer\PHPMailer\PHPMailer;

class OlzMailer extends PHPMailer {
    use \Psr\Log\LoggerAwareTrait;
    // TODO: Those are not actually used. It's just to avoid a test error.
    public const UTILS = [
        'emailUtils',
        'envUtils',
        'logger',
    ];

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
        if ($config['no_header'] ?? false) {
            $html_header = "";
        } else {
            $this->AddEmbeddedImage(__DIR__.'/../../../public/icns/olz_logo_schwarzweiss_300.png', 'olz_logo');
            $html_header = <<<'ZZZZZZZZZZ'
            <div style="text-align: right; float: right;">
                <img src="cid:olz_logo" alt="" style="width:150px;" />
            </div>
            <br /><br /><br />
            ZZZZZZZZZZ;
        }
        if ($config['no_unsubscribe'] ?? false) {
            $html_unsubscribe = "";
            $text_unsubscribe = "";
        } else {
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
            $html_unsubscribe = <<<ZZZZZZZZZZ
            <br /><br />
            <hr style="border: 0; border-top: 1px solid black;">
            Abmelden? <a href="{$unsubscribe_this_url}">Keine solchen E-Mails mehr</a> oder <a href="{$unsubscribe_all_url}">Keine E-Mails von OL Zimmerberg mehr</a>
            ZZZZZZZZZZ;
            $text_unsubscribe = <<<ZZZZZZZZZZ

            ---
            Abmelden?
            Keine solchen E-Mails mehr: {$unsubscribe_this_url}
            Keine E-Mails von OL Zimmerberg mehr: {$unsubscribe_all_url}
            ZZZZZZZZZZ;
        }
        $this->Subject = "{$title}";
        $this->Body = <<<ZZZZZZZZZZ
        {$html_header}
        {$html_text}
        {$html_unsubscribe}
        ZZZZZZZZZZ;
        $this->AltBody = <<<ZZZZZZZZZZ
        {$text}
        {$text_unsubscribe}
        ZZZZZZZZZZ;
    }

    public function send() {
        try {
            parent::send();
        } catch (Exception $e) {
            $this->logger->warning("{$this->ErrorInfo}");
            $this->waitSomeTime();
            try {
                parent::send();
            } catch (Exception $e) {
                $this->logger->critical("{$this->ErrorInfo}");
                throw $e;
            }
        }
        // @codeCoverageIgnoreStart
        // Reason: Email cannot be sent in tests.
    }

    // @codeCoverageIgnoreEnd

    // @codeCoverageIgnoreStart
    // Reason: No time to wait in tests.
    protected function waitSomeTime() {
        sleep(10);
    }

    // @codeCoverageIgnoreEnd
}
