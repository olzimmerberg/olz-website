<?php

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\HtmlRenderer;
use PhpImap\Mailbox;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../GeneralUtils.php';
require_once __DIR__.'/OlzMailer.php';

class EmailUtils {
    use Psr\Log\LoggerAwareTrait;

    private $generalUtils;
    private $envUtils;

    public function __construct($envUtils) {
        $this->generalUtils = new GeneralUtils();
        $this->envUtils = $envUtils;
    }

    public function getImapMailbox() {
        $imap_host = $this->envUtils->getImapHost();
        $imap_port = $this->envUtils->getImapPort();
        $imap_username = $this->envUtils->getImapUsername();
        $imap_password = $this->envUtils->getImapPassword();

        $mailbox_name = "{{$imap_host}:{$imap_port}}";
        // Documentation at https://github.com/barbushin/php-imap
        return new Mailbox(
            "{$mailbox_name}INBOX",
            $imap_username,
            $imap_password
        );
    }

    public function createEmail() {
        $mail = new OlzMailer($this, $this->envUtils, true);

        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = $this->envUtils->getSmtpHost();
        $mail->SMTPAuth = true;
        $mail->Username = $this->envUtils->getSmtpUsername();
        $mail->Password = $this->envUtils->getSmtpPassword();
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = intval($this->envUtils->getSmtpPort());

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom($this->envUtils->getSmtpFrom(), 'OL Zimmerberg');

        $mail->setLogger($this->logger);

        return $mail;
    }

    public function encryptEmailReactionToken($data) {
        $plaintext = json_encode($data);
        $algo = 'aes-256-gcm';
        $key = $this->envUtils->getEmailReactionKey();
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
        $key = $this->envUtils->getEmailReactionKey();
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

        $logger = $_CONFIG->getLogsUtils()->getLogger('EmailUtils');
        $email_utils = new self($_CONFIG);
        $email_utils->setLogger($logger);

        return $email_utils;
    }
}

function getEmailUtilsFromEnv() {
    // @codeCoverageIgnoreStart
    // Reason: functions cannot be covered.
    return EmailUtils::fromEnv();
    // @codeCoverageIgnoreEnd
}
