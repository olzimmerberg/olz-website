<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\GeneralUtils;

class FakeEmailUtils {
    use \Psr\Log\LoggerAwareTrait;

    public $email_verification_emails_sent = [];
    public $send_email_verification_email_error;

    public $client;
    public $olzMailer;

    public function __construct() {
        $this->client = new FakeImapClient();
        $this->olzMailer = new FakeOlzMailer();
    }

    public function sendEmailVerificationEmail($user) {
        if ($this->send_email_verification_email_error !== null) {
            if ($this->logger) {
                $this->logger->error('Error sending fake verification email');
            }
            throw $this->send_email_verification_email_error;
        }
        $this->email_verification_emails_sent[] = ['user' => $user];
    }

    public function getImapClient() {
        return $this->client;
    }

    public function createEmail() {
        $mailer = $this->olzMailer;
        $mailer->setFrom('fake@test.olzimmerberg.ch', 'OL Zimmerberg');
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
