<?php

namespace Olz\Utils;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymfonyUtils {
    use WithUtilsTrait;

    protected static $application;

    public function callCommand(
        string $command_name,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        $application = $this->getApplication();
        $application->setAutoExit(false);
        $command = $application->find($command_name);
        $return_code = $command->run($input, $output);
        if ($return_code === Command::FAILURE) {
            throw new \Exception("Command {$command_name} failed.");
        }
        if ($return_code === Command::INVALID) {
            throw new \Exception("Command {$command_name} called with invalid arguments.");
        }
        if ($return_code !== Command::SUCCESS) {
            throw new \Exception("Command {$command_name} failed with unknown code: {$return_code}.");
        }
    }

    public function getApplication() {
        if (self::$application !== null) {
            return self::$application;
        }

        global $kernel;

        if (!$kernel) {
            return null;
        }

        $application = new Application($kernel);
        self::$application = $application;
        return $application;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
