<?php

namespace Olz\Command\Common;

use Olz\Utils\LogsUtils;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class OlzCommand extends Command {
    use WithUtilsTrait;

    abstract protected function getAllowedAppEnvs(): array;

    abstract protected function handle(InputInterface $input, OutputInterface $output): int;

    protected function execute(InputInterface $input, OutputInterface $output): int {
        LogsUtils::activateLogger($this->log());
        try {
            $app_env = $this->getAppEnv();
            $allowed_app_envs = $this->getAllowedAppEnvs();
            $allowed = false;
            foreach ($allowed_app_envs as $allowed_app_env) {
                if ($app_env === $allowed_app_env) {
                    $allowed = true;
                }
            }
            if (!$allowed) {
                $message = "Command {$this->getIdent()} not allowed in app env {$app_env}.";
                $this->log()->notice($message);
                $output->writeln($message);
                LogsUtils::deactivateLogger($this->log());
                return Command::INVALID;
            }
            $this->log()->info("Running command {$this->getIdent()}...");
            $status = $this->handle($input, $output);
            if ($status === Command::SUCCESS) {
                $this->log()->info("Successfully ran command {$this->getIdent()}.");
            } elseif ($status === Command::FAILURE) {
                $this->log()->notice("Failed running command {$this->getIdent()}.");
            } elseif ($status === Command::INVALID) {
                $this->log()->notice("Command {$this->getIdent()} called with invalid arguments.");
            } else {
                $this->log()->warning("Command {$this->getIdent()} finished with unknown status {$status}.");
            }
        } catch (\Exception $exc) {
            $message = "Error running command {$this->getIdent()}: {$exc->getMessage()}.";
            $output->writeln($message);
            $this->log()->error($message, [$exc]);
            $status = Command::FAILURE;
        }
        LogsUtils::deactivateLogger($this->log());
        return $status;
    }

    protected function getAppEnv(): string {
        $olz_app_env = $this->envUtils()->getAppEnv();
        $symfony_app_env = $_ENV['APP_ENV'] ?? null;
        if ($olz_app_env !== $symfony_app_env) {
            throw new \Exception("OLZ and symfony app env do not match ({$olz_app_env} vs. {$symfony_app_env})");
        }
        return $olz_app_env;
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
