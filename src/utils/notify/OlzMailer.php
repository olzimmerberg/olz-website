<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__.'/../../config/vendor/autoload.php';

class OlzMailer extends PHPMailer {
    public function configure($user, $title, $text) {
        $user_email = $user->getEmail();
        // TODO: Check if verified?
        $user_full_name = $user->getFullName();
        $this->addAddress($user_email, $user_full_name);
        $html_text = nl2br($text);
        $this->isHTML(true);
        $this->Subject = "[OLZ] {$title}";
        $this->Body = "HTML-<b>Test</b>,<br />\n{$html_text}";
        $this->AltBody = "{$text}";
    }

    public function send() {
        try {
            parent::send();
        } catch (Exception $e) {
            // TODO: Logging
            // $this->logger->critical("{$mail->ErrorInfo}");
            throw $e;
        }
    }
}
