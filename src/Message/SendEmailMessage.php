<?php

namespace Olz\Message;

class SendEmailMessage {
    public function __construct(
        private string $to,
        private string $subject,
        private string $content,
    ) {
    }

    public function getTo(): string {
        return $this->to;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function getContent(): string {
        return $this->content;
    }
}
