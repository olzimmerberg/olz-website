<?php

namespace Olz\Command\Common;

use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class OlzCommand extends Command {
    use WithUtilsTrait;

    protected ?OutputInterface $output = null;

    /** @return array<string> */
    abstract protected function getAllowedAppEnvs(): array;

    abstract protected function handle(InputInterface $input, OutputInterface $output): int;

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->output = $output;

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
                $this->logAndOutput("Command {$this->getIdent()} not allowed in app env {$app_env}.", level: 'notice');
                return Command::INVALID;
            }
            $this->logAndOutput("Running command {$this->getIdent()}...");
            $status = $this->handle($input, $output);
            if ($status === Command::SUCCESS) {
                $this->logAndOutput("Successfully ran command {$this->getIdent()}.");
            } elseif ($status === Command::FAILURE) {
                $this->logAndOutput("Failed running command {$this->getIdent()}.", level: 'notice');
            } elseif ($status === Command::INVALID) {
                $this->logAndOutput("Command {$this->getIdent()} called with invalid arguments.", level: 'notice');
            } else {
                $this->logAndOutput("Command {$this->getIdent()} finished with unknown status {$status}.", level: 'warning');
            }
        } catch (\Exception $exc) {
            $this->logAndOutput("Error running command {$this->getIdent()}: {$exc->getMessage()}.", level: 'error');
            $status = Command::FAILURE;
        }
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

    protected function getIdent(): string {
        return get_called_class();
    }

    /**
     * @param array<mixed>                                                             $context
     * @param 'alert'|'critical'|'debug'|'emergency'|'error'|'info'|'notice'|'warning' $level
     */
    protected function logAndOutput(string $message, array $context = [], string $level = 'info'): void {
        $this->log()->log($level, $message, $context);
        if ($this->output !== null) {
            $this->output->writeln($message);
        }
    }
}
