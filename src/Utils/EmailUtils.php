<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Olz\Exceptions\RecaptchaDeniedException;
use Webklex\PHPIMAP\ClientManager;

class EmailUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'generalUtils',
        'log',
        'recaptchaUtils',
    ];

    public function sendEmailVerificationEmail($user, $token) {
        if (!$this->recaptchaUtils()->validateRecaptchaToken($token)) {
            $this->log()->warning("reCaptcha token was invalid");
            throw new RecaptchaDeniedException("ReCaptcha Token ist ungültig");
        }

        $user_id = $user->getId();
        $email_verification_token = $this->getRandomEmailVerificationToken();
        $user->setEmailVerificationToken($email_verification_token);
        $verify_email_token = urlencode($this->encryptEmailReactionToken([
            'action' => 'verify_email',
            'user' => $user_id,
            'email' => $user->getEmail(),
            'token' => $email_verification_token,
        ]));
        $base_url = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $verify_email_url = "{$base_url}{$code_href}email_reaktion.php?token={$verify_email_token}";
        $text = <<<ZZZZZZZZZZ
        **!!! Falls du nicht soeben auf olzimmerberg.ch deine E-Mail-Adresse bestätigen wolltest, lösche diese E-Mail !!!**

        Hallo {$user->getFirstName()},

        *Um deine E-Mail-Adresse zu bestätigen*, klicke [hier]({$verify_email_url}) oder auf folgenden Link:

        {$verify_email_url}

        ZZZZZZZZZZ;
        $config = [
            'no_unsubscribe' => true,
        ];

        try {
            $email = $this->createEmail();
            $email->configure($user, "[OLZ] E-Mail bestätigen", $text, $config);
            $email->send();
            $this->log()->info("Email verification email sent to user ({$user_id}).");
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $full_message = "Error sending email verification email to user ({$user_id}): {$message}";
            $this->log()->critical($full_message);
            throw new \Exception($full_message);
        }
    }

    protected function getRandomEmailVerificationToken() {
        return $this->generalUtils()->base64EncodeUrl(openssl_random_pseudo_bytes(6));
    }

    public function getImapClient() {
        $imap_host = $this->envUtils()->getImapHost();
        $imap_port = $this->envUtils()->getImapPort();
        $imap_flags = $this->envUtils()->getImapFlags();
        $imap_username = $this->envUtils()->getImapUsername();
        $imap_password = $this->envUtils()->getImapPassword();

        $cm = new ClientManager();
        return $cm->make([
            'host' => $imap_host,
            'port' => $imap_port,
            // TODO: Load encryption, validate_cert and protocol from config.
            'encryption' => 'ssl',
            'validate_cert' => false,
            'username' => $imap_username,
            'password' => $imap_password,
            'protocol' => 'imap',
        ]);

        // Documentation at:
        //    https://www.php-imap.com/api/client
        //    https://github.com/Webklex/php-imap
    }

    public function createEmail() {
        $mail = new OlzMailer($this, $this->envUtils(), true);

        if ($this->envUtils()->getSmtpHost() !== null) {
            $mail->SMTPDebug = $this->envUtils()->getSmtpDebug();
            $mail->isSMTP();
            $mail->Host = $this->envUtils()->getSmtpHost();
            $mail->SMTPAuth = ($this->envUtils()->getSmtpUsername() !== null);
            $mail->Username = $this->envUtils()->getSmtpUsername();
            $mail->Password = $this->envUtils()->getSmtpPassword();
            $mail->SMTPSecure = $this->envUtils()->getSmtpSecure();
            $mail->Port = intval($this->envUtils()->getSmtpPort());
        } else {
            $mail->isSendmail();
        }

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom($this->envUtils()->getSmtpFrom(), 'OL Zimmerberg');

        $mail->setLogger($this->log());

        return $mail;
    }

    public function encryptEmailReactionToken($data) {
        $key = $this->envUtils()->getEmailReactionKey();
        return $this->generalUtils()->encrypt($key, $data);
    }

    public function decryptEmailReactionToken($token) {
        $key = $this->envUtils()->getEmailReactionKey();
        return $this->generalUtils()->decrypt($key, $token);
    }

    public function renderMarkdown($markdown) {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $converter = new MarkdownConverter($environment);
        $rendered = $converter->convert($markdown);
        return strval($rendered);
    }
}
