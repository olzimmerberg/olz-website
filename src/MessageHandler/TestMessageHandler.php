<?php

namespace Olz\MessageHandler;

use Olz\Entity\Throttling;
use Olz\Message\TestMessage;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestMessageHandler {
    use WithUtilsTrait;

    public function __invoke(TestMessage $message): void {
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('test_message', $this->dateUtils()->getIsoNow());
        $this->log()->info("Handled TestMessage: {$message->getIdent()}");
    }
}
