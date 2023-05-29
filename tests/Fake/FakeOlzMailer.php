<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeOlzMailer {
    public $provoke_error = false;
    public $emails_sent = [];
    public $user;
    public $Subject;
    public $Body;
    public $AltBody;
    public $from;
    public $reply_to;
    public $headers = [];
    public $attachments = [];

    public $Sender;

    public function configure($user, $title, $text) {
        $this->user = $user;
        $this->Subject = $title;
        $this->Body = $text;
        $this->AltBody = $text;
    }

    public function setFrom($address, $name) {
        $this->from = [$address, $name];
    }

    public function addAddress($address, $name) {
        $this->headers[] = ['To', "{$name} <{$address}>"];
    }

    public function addReplyTo($address, $name) {
        $this->reply_to = [$address, $name];
    }

    public function addCustomHeader($key, $value) {
        $this->headers[] = [$key, $value];
    }

    public function addAttachment($path, $name) {
        $this->attachments[] = [$path, $name];
    }

    public function isHTML($is_html) {
    }

    public function send() {
        $title_provokes_error = str_contains(
            $this->Subject, 'provoke_error');
        $text_provokes_error = str_contains(
            $this->Body, 'provoke_error');
        if ($this->provoke_error || $title_provokes_error || $text_provokes_error) {
            throw new \Exception("Provoked Mailer Error");
        }
        $this->emails_sent[] = [
            'user' => $this->user,
            'from' => $this->from,
            'sender' => $this->Sender,
            'replyTo' => $this->reply_to,
            'headers' => $this->headers,
            'subject' => $this->Subject,
            'body' => $this->Body,
            'altBody' => $this->AltBody,
            'attachments' => $this->attachments,
        ];
    }
}
