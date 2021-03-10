<?php

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\HtmlRenderer;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../GeneralUtils.php';
require_once __DIR__.'/OlzMailer.php';

class EmailUtils {
    private $generalUtils;
    private $serverConfig;

    public function __construct($serverConfig) {
        $this->generalUtils = new GeneralUtils();
        $this->serverConfig = $serverConfig;
    }

    public function createEmail() {
        $mail = new OlzMailer($this, $this->serverConfig, true);

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

    public function encryptEmailReactionToken($data) {
        $plaintext = json_encode($data);
        $algo = 'aes-256-gcm';
        $key = $this->serverConfig->getEmailReactionKey();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $this->generalUtils->base64EncodeUrl(json_encode([
            'algo' => $algo,
            'iv' => $this->generalUtils->base64EncodeUrl($iv),
            'tag' => $this->generalUtils->base64EncodeUrl($tag),
            'ciphertext' => $this->generalUtils->base64EncodeUrl($ciphertext),
        ]));
    }

    public function decryptEmailReactionToken($token) {
        $decrypt_data = json_decode($this->generalUtils->base64DecodeUrl($token), true);
        if (!$decrypt_data) {
            return null;
        }
        $ciphertext = $this->generalUtils->base64DecodeUrl($decrypt_data['ciphertext']);
        $algo = $decrypt_data['algo'] ?? 'aes-256-gcm';
        $key = $this->serverConfig->getEmailReactionKey();
        $iv = $this->generalUtils->base64DecodeUrl($decrypt_data['iv']);
        $tag = $this->generalUtils->base64DecodeUrl($decrypt_data['tag']);
        $plaintext = openssl_decrypt($ciphertext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return json_decode($plaintext, true);
    }

    public function renderMarkdown($markdown) {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->setConfig([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);

        $parser = new DocParser($environment);
        $document = $parser->parse($markdown);

        $html_renderer = new HtmlRenderer($environment);
        return $html_renderer->renderBlock($document);
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
