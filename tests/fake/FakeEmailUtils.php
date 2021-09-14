<?php

use PhpImap\Exceptions\ConnectionException;

require_once __DIR__.'/../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../src/utils/GeneralUtils.php';

class FakeEmailUtils {
    use Psr\Log\LoggerAwareTrait;

    public function __construct() {
        $this->mailbox = new FakeMailbox();
        $this->olzMailer = new FakeOlzMailer();
    }

    public function getImapMailbox() {
        return $this->mailbox;
    }

    public function createEmail() {
        return $this->olzMailer;
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

class FakeMailbox {
    public $connection_exception = false;
    public $mail_dict = [];
    public $deleted_mail_dict = [];
    public $expunged_mail_dict = [];

    public function setAttachmentsIgnore($should_ignore_attachments) {
    }

    public function searchMailbox($query) {
        if ($this->connection_exception) {
            throw new ConnectionException("Host not found or something.");
        }
        if ($query === 'ALL') {
            return array_keys($this->mail_dict);
        }
        throw new Exception("Expected 'ALL' query to searchMailbox");
    }

    public function getMail($mail_id, $should_mark_read) {
        return $this->mail_dict[$mail_id];
    }

    public function deleteMail($mail_id) {
        $this->deleted_mail_dict[$mail_id] = true;
    }

    public function expungeDeletedMails() {
        $this->expunged_mail_dict = $this->deleted_mail_dict;
    }
}

class FakeOlzMailer {
    public $emails_sent = [];
    public $email_to_send;
    public $reply_to;

    public function configure($user, $title, $text) {
        $this->email_to_send = [$user, $title, $text];
    }

    public function addReplyTo($address, $name) {
        $this->reply_to = [$address, $name];
    }

    public function send() {
        if (str_contains($this->email_to_send[1], 'provoke_error')) {
            throw new Exception("Provoked Error");
        }
        $this->emails_sent[] = $this->email_to_send;
    }
}
