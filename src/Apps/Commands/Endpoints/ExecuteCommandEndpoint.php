<?php

namespace Olz\Apps\Commands\Endpoints;

use Doctrine\DBAL\Exception\DriverException;
use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ExecuteCommandEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'ExecuteCommandEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'error' => new FieldTypes\BooleanField(['allow_null' => false]),
            'output' => new FieldTypes\StringField(['allow_null' => false]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'command' => new FieldTypes\StringField(['allow_null' => false]),
            'argv' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    protected function handle($input) {
        $command_name = $input['command'];
        try {
            $has_access = $this->authUtils()->hasPermission('commands');
            $has_command_access = $this->authUtils()->hasPermission("command_{$command_name}");
            if (!$has_access && !$has_command_access) {
                throw new HttpError(403, "Kein Zugriff!");
            }
        } catch (DriverException $exc) {
            // Could be a migration issue
            // => if the command is db-migrate or db-reset, continue nevertheless!
            $should_continue = (
                $command_name === 'olz:db-migrate'
                || $command_name === 'olz:db-reset'
            );
            if (!$should_continue) {
                throw $exc;
            }
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        $argv = $input['argv'] ? preg_split('/\s+/', $input['argv']) : [];
        $command_input = new ArgvInput(['bin/console', $command_name, ...$argv]);
        $command_input->setInteractive(false);
        $command_output = new BufferedOutput();
        try {
            $this->symfonyUtils()->callCommand($command_name, $command_input, $command_output);
            $output = $command_output->fetch();
            $this->log()->info("Command {$command_name} successfully executed via endpoint.");
            return [
                'error' => false,
                'output' => $output ? $output : '(no output)',
            ];
        } catch (\Throwable $th) {
            $this->log()->notice("Failed to execute command {$command_name} via endpoint.");
            $output = $command_output->fetch();
            return [
                'error' => true,
                'output' => $output."\n".$th->getMessage(),
            ];
        }
    }
}
