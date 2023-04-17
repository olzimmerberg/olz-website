<?php

namespace Olz\Command\Common;

use Olz\Utils\LogsUtils;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class OlzCommand extends Command {
    use WithUtilsTrait;

    abstract protected function handle(InputInterface $input, OutputInterface $output): int;

    protected function execute(InputInterface $input, OutputInterface $output): int {
        LogsUtils::activateLogger($this->log());
        try {
            $this->log()->info("Running command {$this->getIdent()}...");
            $status = $this->handle($input, $output);
            if ($status === Command::SUCCESS) {
                $this->log()->info("Successfully ran command {$this->getIdent()}.");
            } elseif ($status === Command::FAILURE) {
                $this->log()->error("Failed running command {$this->getIdent()}.");
            } elseif ($status === Command::INVALID) {
                $this->log()->error("Command {$this->getIdent()} called with invalid arguments.");
            } else {
                $this->log()->error("Command {$this->getIdent()} finished with unknown status {$status}.");
            }
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->error("Error running command {$this->getIdent()}: {$message}.", [$exc]);
            $status = Command::FAILURE;
        }
        LogsUtils::deactivateLogger($this->log());
        return $status;
    }

    protected function callCommand(
        string $command_name,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        $command = $this->getApplication()->find($command_name);
        $return_code = $command->run($input, $output);
        if ($return_code !== Command::SUCCESS) {
            throw new \Exception("Command {$command_name} failed with code: {$return_code}");
        }
    }

    protected function getIdent(): string {
        return get_called_class();
    }
}
