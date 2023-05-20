<?php

namespace Olz\MessageHandler;

use Olz\Message\SendEmailMessage;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailMessageHandler {
    use WithUtilsTrait;

    public function __invoke(SendEmailMessage $message) {
        $this->emailUtils()->setLogger($this->log());
        $email = $this->emailUtils()->createEmail();
        $email->addAddress($message->getTo());
        $email->isHTML(false);
        $email->Subject = "[OLZ] {$message->getSubject()}";
        $email->Body = $message->getContent();
        $email->send();
        $this->log()->info("Handled SendEmailMessage");
    }
}
