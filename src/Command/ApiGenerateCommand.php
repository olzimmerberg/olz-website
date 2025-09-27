<?php

namespace Olz\Command;

use Olz\Api\OlzApi;
use Olz\Command\Common\OlzCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:api-generate')]
class ApiGenerateCommand extends OlzCommand {
    public function __construct(protected OlzApi $olzApi) {
        parent::__construct();
    }

    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $this->olzApi->generate();
        return Command::SUCCESS;
    }
}
