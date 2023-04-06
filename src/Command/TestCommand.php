<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:test')]
class TestCommand extends OlzCommand {
    protected function handle(InputInterface $input, OutputInterface $output): int {
        $data_path = $this->envUtils()->getDataPath();
        $mysql_host = $this->envUtils()->getMysqlHost();
        $smtp_from = $this->envUtils()->getSmtpFrom();
        $app_env = $_ENV['APP_ENV'];
        $info = <<<ZZZZZZZZZZ
        Data path: {$data_path}
        MySQL host: {$mysql_host}
        SMTP from: {$smtp_from}
        App env: {$app_env}
        ZZZZZZZZZZ;
        $output->writeln($info);
        $this->log()->info($info);
        return Command::SUCCESS;
    }
}
