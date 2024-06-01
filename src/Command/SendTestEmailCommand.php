<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Message\SendEmailMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'olz:send-test-email')]
class SendTestEmailCommand extends OlzCommand {
    protected MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $bus) {
        parent::__construct();
        $this->messageBus = $bus;
    }

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $base_href = $this->envUtils()->getBaseHref();
        $to = 'simon+olztestemail@hatt.style';
        $subject = 'Test mail';
        $content = "A test mail has been sent from {$base_href}!";
        $message = new SendEmailMessage($to, $subject, $content);
        $this->messageBus->dispatch($message);
        $this->log()->info("Test SendEmailMessage dispatched.");
        return Command::SUCCESS;
    }
}
