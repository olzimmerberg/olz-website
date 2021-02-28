<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__.'/../../config/vendor/autoload.php';

class OlzMailer extends PHPMailer {
    public function configure($user, $title, $text) {
        $user_email = $user->getEmail();
        // TODO: Check if verified?
        $user_full_name = $user->getFullName();
        $user_first_name = $user->getFirstName();
        $this->addAddress($user_email, $user_full_name);
        $html_text = nl2br($text);
        $this->isHTML(true);
        $this->AddEmbeddedImage(__DIR__.'/../../icns/olz_logo_schwarzweiss_300.png', 'olz_logo');
        $this->Subject = "[OLZ] {$title}";
        $this->Body = <<<ZZZZZZZZZZ
        <div style="text-align: right; float: right;">
            <img src="cid:olz_logo" alt="" style="width:150px;" />
        </div>
        Hallo <b>{$user_first_name}</b>,<br />
        {$html_text}<br />
        <br />
        <hr style="border: 0; border-top: 1px solid black;">
        Abmelden? <a href="https://olzimmerberg.ch/TODO">Keine solchen E-Mails mehr</a> - <a href="https://olzimmerberg.ch/TODO">Keine E-Mails von OL Zimmerberg mehr</a>
        ZZZZZZZZZZ;
        $this->AltBody = <<<ZZZZZZZZZZ
        Hallo {$user_first_name},

        {$text}

        ---
        Abmelden?
        Keine solchen E-Mails mehr: https://olzimmerberg.ch/TODO
        Keine E-Mails von OL Zimmerberg mehr: https://olzimmerberg.ch/TODO
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
    }
}
