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

    public function configure($user, $title, $text) {
        $this->user = $user;
        $this->Subject = $title;
        $this->Body = $text;
        $this->AltBody = $text;
    }

    public function setFrom($address, $name) {
        $this->from = [$address, $name];
    }

    public function addReplyTo($address, $name) {
        $this->reply_to = [$address, $name];
    }

    public function send() {
        $title_provokes_error = str_contains(
            $this->Subject, 'provoke_error');
        $text_provokes_error = str_contains(
            $this->Body, 'provoke_error');
        if ($this->provoke_error || $title_provokes_error || $text_provokes_error) {
            throw new \Exception("Provoked Mailer Error");
        }
        $this->emails_sent[] = [$this->user, $this->Subject, $this->Body, $this->AltBody];
    }
}
