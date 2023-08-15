<?php

namespace Olz\MessageHandler;

use Olz\Message\SendEmailMessage;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class SendEmailMessageHandler {
    use WithUtilsTrait;

    public function __invoke(SendEmailMessage $message) {
        $email = (new Email())
            ->to($message->getTo())
            ->subject("[OLZ] {$message->getSubject()}")
            ->text($message->getContent())
        ;

        try {
            $this->mailer->send($email);
            $this->log()->info("Handled SendEmailMessage");
        } catch (\Throwable $th) {
            $this->log()->error("Error handling SendEmailMessage", [$th]);
        }
    }
}
