<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\EmailUtils;
use Olz\Utils\GeneralUtils;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Query\WhereQuery;
use Webklex\PHPIMAP\Support\MessageCollection;

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

    public function getImapClient(): Client {
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

class FakeImapClient extends Client {
    public $exception = false;
    public $folders = [];
    public $is_connected = false;

    public function __construct() {
    }

    public function createFolder(string $folder_path, bool $expunge = true, bool $utf7 = false): Folder {
        return new Folder($this, $folder_path, '/', []);
    }

    public function connect(): Client {
        if ($this->exception) {
            throw new \Exception("Failed at something");
        }
        $this->is_connected = true;
        return $this;
    }

    public function getFolderByPath($folder_path, bool $utf7 = false, bool $soft_fail = false): Folder {
        return new FakeImapFolder($this, $this->folders[$folder_path] ?? []);
    }
}

class FakeImapFolder extends Folder {
    public function __construct(
        protected Client $client,
        public $mails = [],
    ) {
    }

    public function messages(array $extensions = []): WhereQuery {
        return new FakeWhereQuery($this->client, $this->mails);
    }
}

class FakeWhereQuery extends WhereQuery {
    public $should_leave_unread = false;
    public $should_fetch_body = true;

    public function __construct(
        protected Client $client,
        public $mails = [],
    ) {
    }

    public function leaveUnread(): WhereQuery {
        $this->should_leave_unread = true;
        return $this;
    }

    public function setFetchBody($value): WhereQuery {
        $this->should_fetch_body = $value;
        return $this;
    }

    public function all() {
        return $this;
    }

    public function get(): MessageCollection {
        return new MessageCollection($this->mails ?? []);
    }
}
