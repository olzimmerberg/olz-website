<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\EmailUtils;
use Olz\Utils\GeneralUtils;

class FakeEmailUtils extends EmailUtils {
    use \Psr\Log\LoggerAwareTrait;

    public $email_verification_emails_sent = [];
    public $send_email_verification_email_error;

    public $client;
    public $olzMailers = [];

    public function __construct() {
        $this->client = new FakeImapClient();
    }

    public function sendEmailVerificationEmail($user, $token) {
        if ($this->send_email_verification_email_error !== null) {
            if ($this->logger) {
                $this->logger->error('Error sending fake verification email');
            }
            throw $this->send_email_verification_email_error;
        }
        $this->email_verification_emails_sent[] = ['user' => $user, 'token' => $token];
    }

    public function getImapClient() {
        return $this->client;
    }

    public function createEmail() {
        $mailer = new FakeOlzMailer();
        $mailer->setFrom('fake@staging.olzimmerberg.ch', 'OL Zimmerberg');
        $this->olzMailers[] = $mailer;
        return $mailer;
    }

    public function encryptEmailReactionToken($data) {
        $general_utils = new GeneralUtils();
        return $general_utils->base64EncodeUrl(json_encode($data));
    }

    public function decryptEmailReactionToken($token) {
        return json_decode($token, true);
    }

    public function renderMarkdown($markdown) {
        return $markdown;
    }

    public function testOnlyEmailsSent() {
        $emails_sent = [];
        foreach ($this->olzMailers as $mailer) {
            $emails_sent = [
                ...$emails_sent,
                ...$mailer->emails_sent,
            ];
        }
        return $emails_sent;
    }
}

class FakeImapClient {
    public $exception = false;
    public $folders = [];
    public $is_connected = false;

    public function createFolder($name) {
    }

    public function connect() {
        if ($this->exception) {
            throw new \Exception("Failed at something");
        }
        $this->is_connected = true;
    }

    public function getFolderByPath($path) {
        return new FakeImapFolder($this->folders[$path] ?? []);
    }
}

class FakeImapFolder {
    public $should_leave_unread = false;
    public $should_fetch_body = true;

    public function __construct(
        public $mails = [],
    ) {
    }

    public function messages() {
        return $this;
    }

    public function leaveUnread() {
        $this->should_leave_unread = true;
    }

    public function setFetchBody($value) {
        $this->should_fetch_body = $value;
    }

    public function all() {
        return $this;
    }

    public function get() {
        return $this->mails;
    }
}
