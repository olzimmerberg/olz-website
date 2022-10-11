<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use PhpImap\Mailbox;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__.'/OlzMailer.php';

class EmailUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'generalUtils',
        'log',
    ];

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
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = $this->envUtils()->getSmtpHost();
            $mail->SMTPAuth = true;
            $mail->Username = $this->envUtils()->getSmtpUsername();
            $mail->Password = $this->envUtils()->getSmtpPassword();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
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
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $this->generalUtils()->base64EncodeUrl(json_encode([
            'algo' => $algo,
            'iv' => $this->generalUtils()->base64EncodeUrl($iv),
            'tag' => $this->generalUtils()->base64EncodeUrl($tag),
            'ciphertext' => $this->generalUtils()->base64EncodeUrl($ciphertext),
        ]));
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
