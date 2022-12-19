<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use PhpImap\Mailbox;

require_once __DIR__.'/OlzMailer.php';

class EmailUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'generalUtils',
        'log',
    ];

    public function sendEmailVerificationEmail($user) {
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

    public function getImapMailbox() {
        $imap_host = $this->envUtils()->getImapHost();
        $imap_port = $this->envUtils()->getImapPort();
        $imap_flags = $this->envUtils()->getImapFlags();
        $imap_username = $this->envUtils()->getImapUsername();
        $imap_password = $this->envUtils()->getImapPassword();

        $mailbox_name = "{{$imap_host}:{$imap_port}{$imap_flags}}";
        // Documentation at https://github.com/barbushin/php-imap
        return new Mailbox(
            "{$mailbox_name}INBOX",
            $imap_username,
            $imap_password
        );
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
        $plaintext = json_encode($data);
        $algo = 'aes-256-gcm';
        $key = $this->envUtils()->getEmailReactionKey();
        $iv = $this->getRandomIvForAlgo($algo);
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $this->generalUtils()->base64EncodeUrl(json_encode([
            'algo' => $algo,
            'iv' => $this->generalUtils()->base64EncodeUrl($iv),
            'tag' => $this->generalUtils()->base64EncodeUrl($tag),
            'ciphertext' => $this->generalUtils()->base64EncodeUrl($ciphertext),
        ]));
    }

    protected function getRandomIvForAlgo($algo) {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
    }

    public function decryptEmailReactionToken($token) {
        $decrypt_data = json_decode($this->generalUtils()->base64DecodeUrl($token), true);
        if (!$decrypt_data) {
            return null;
        }
        $ciphertext = $this->generalUtils()->base64DecodeUrl($decrypt_data['ciphertext']);
        $algo = $decrypt_data['algo'] ?? 'aes-256-gcm';
        $key = $this->envUtils()->getEmailReactionKey();
        $iv = $this->generalUtils()->base64DecodeUrl($decrypt_data['iv']);
        $tag = $this->generalUtils()->base64DecodeUrl($decrypt_data['tag']);
        $plaintext = openssl_decrypt($ciphertext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return json_decode($plaintext, true);
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
