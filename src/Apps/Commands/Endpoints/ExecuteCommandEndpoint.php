<?php

namespace Olz\Apps\Commands\Endpoints;

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
        $has_access = $this->authUtils()->hasPermission('commands');
        $command_name = $input['command'];
        $has_command_access = $this->authUtils()->hasPermission("command_{$command_name}");
        if (!$has_access && !$has_command_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        $argv = $input['argv'] ? preg_split('/\s+/', $input['argv']) : [];
        $command_input = new ArgvInput(['bin/console', $command_name, ...$argv]);
        $command_output = new BufferedOutput();
        try {
            $this->symfonyUtils()->callCommand($command_name, $command_input, $command_output);
            $output = $command_output->fetch();
            return [
                'output' => $output ? $output : '(no output)',
            ];
        } catch (\Throwable $th) {
            $output = $command_output->fetch();
            return [
                'output' => $output."\n".$th->getMessage(),
            ];
        }
    }
}
