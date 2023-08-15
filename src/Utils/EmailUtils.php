<?php

namespace Olz\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Olz\Entity\User;
use Olz\Exceptions\RecaptchaDeniedException;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Webklex\PHPIMAP\Client;
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

    public function getImapClient(): Client {
        $env_utils = $this->envUtils();
        $imap_host = $env_utils->getImapHost();
        $imap_port = $env_utils->getImapPort();
        $imap_flags = $env_utils->getImapFlags();
        $imap_username = $env_utils->getImapUsername();
        $imap_password = $env_utils->getImapPassword();

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

    /** @deprecated TODO: Replace with symfony mailer */
    public function createEmail() {
        $mail = new OlzMailer(true);

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

    public function buildOlzEmail(Email $email, User $user, string $text, array $config): Email {
        // TODO: Check if verified?
        $user_id = $user->getId();
        $email = $email->to($this->getUserAddress($user));
        $html_text = $this->renderMarkdown($text);
        $html_header = "";
        if (!($config['no_header'] ?? false)) {
            $email = $email->addPart((new DataPart(new File(__DIR__.'/../../assets/icns/olz_logo_schwarzweiss_300.png'), 'olz_logo', 'image/png'))->asInline());
            $html_header = <<<'ZZZZZZZZZZ'
            <div style="text-align: right; float: right;">
                <img src="cid:olz_logo" alt="" style="width:150px;" />
            </div>
            <br /><br /><br />
            ZZZZZZZZZZ;
        }
        $html_unsubscribe = "";
        $text_unsubscribe = "";
        if (!($config['no_unsubscribe'] ?? false)) {
            if (!isset($config['notification_type'])) {
                $this->log()->warning("E-Mail has no notification_type (to user: {$user_id}): {$html_text}");
            }
            $unsubscribe_this_token = urlencode($this->encryptEmailReactionToken([
                'action' => 'unsubscribe',
                'user' => $user_id,
                'notification_type' => $config['notification_type'] ?? null,
            ]));
            $unsubscribe_all_token = urlencode($this->encryptEmailReactionToken([
                'action' => 'unsubscribe',
                'user' => $user_id,
                'notification_type_all' => true,
            ]));
            $base_url = $this->envUtils()->getBaseHref();
            $code_href = $this->envUtils()->getCodeHref();
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
        $email = $email->text(<<<ZZZZZZZZZZ
            {$text}
            {$text_unsubscribe}
            ZZZZZZZZZZ);
        return $email->html(<<<ZZZZZZZZZZ
            {$html_header}
            {$html_text}
            {$html_unsubscribe}
            ZZZZZZZZZZ);
    }

    public function getUserAddress(User $user): Address {
        $user_email = $user->getEmail();
        $user_full_name = $user->getFullName();
        return new Address($user_email, $user_full_name);
    }

    public function getComparableEmail(Email $email): string {
        $from = $this->arr2str($email->getFrom());
        $reply_to = $this->arr2str($email->getReplyTo());
        $to = $this->arr2str($email->getTo());
        $cc = $this->arr2str($email->getCc());
        $bcc = $this->arr2str($email->getBcc());
        $subject = $email->getSubject();
        $text_body = $email->getTextBody() ?? '(no text body)';
        $html_body = $email->getHtmlBody() ?? '(no html body)';
        $attachments = implode('', array_map(function (DataPart $data_part) {
            return "\n".$data_part->getFilename();
        }, $email->getAttachments()));

        return <<<ZZZZZZZZZZ
        From: {$from}
        Reply-To: {$reply_to}
        To: {$to}
        Cc: {$cc}
        Bcc: {$bcc}
        Subject: {$subject}

        {$text_body}

        {$html_body}
        {$attachments}
        ZZZZZZZZZZ;
    }

    public function getComparableEnvelope(Envelope $envelope): string {
        $sender = $envelope->getSender()->toString();
        $recipients = $this->arr2str($envelope->getRecipients());
        return <<<ZZZZZZZZZZ
        Sender: {$sender}
        Recipients: {$recipients}
        ZZZZZZZZZZ;
    }

    protected function arr2str(array $arr): string {
        return implode(', ', array_map(function ($item) {
            return $item->toString();
        }, $arr));
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
