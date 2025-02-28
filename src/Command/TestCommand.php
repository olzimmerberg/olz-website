<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Throttling;
use Olz\Message\TestMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'olz:test')]
class TestCommand extends OlzCommand {
    protected MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $bus) {
        parent::__construct();
        $this->messageBus = $bus;
    }

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $data_path = $this->envUtils()->getDataPath();
        $private_path = $this->envUtils()->getPrivatePath();
        $mysql_host = $this->envUtils()->getMysqlHost();
        $smtp_from = $this->envUtils()->getSmtpFrom();
        $app_env = $_ENV['APP_ENV'];
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $pretty_throttlings = '';
        foreach ($throttling_repo->findAll() as $throttling) {
            $name = $throttling->getEventName();
            $pretty_date = $throttling->getLastOccurrence()?->format('Y-m-d H:i:s');
            $pretty_throttlings .= "    {$name}: {$pretty_date}\n";
        }

        $info = <<<ZZZZZZZZZZ
            Data path: {$data_path}
            Private path: {$private_path}
            MySQL host: {$mysql_host}
            SMTP from: {$smtp_from}
            App env: {$app_env}
            Throttlings:
            {$pretty_throttlings}
            ZZZZZZZZZZ;
        $output->writeln($info);
        $this->log()->info($info);

        $this->messageBus->dispatch(new TestMessage());

        return Command::SUCCESS;
    }
}
