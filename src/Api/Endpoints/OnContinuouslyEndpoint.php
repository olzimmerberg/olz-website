<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     authenticityCode: non-empty-string,
 *   },
 *   ?array{}
 * >
 */
class OnContinuouslyEndpoint extends OlzTypedEndpoint {
    public function parseInput(Request $request): mixed {
        return [
            'authenticityCode' => $request->query->get('authenticityCode'),
        ];
    }

    protected function handle(mixed $input): mixed {
        $expected_code = $this->envUtils()->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        $command_input = new ArrayInput([]);
        $command_output = new BufferedOutput();
        $this->symfonyUtils()->callCommand('olz:on-continuously', $command_input, $command_output);

        return [];
    }
}
