<?php

namespace Olz\Apps\SearchEngines\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{},
 *   array{username: string, password: string}
 * >
 */
class GetAppSearchEnginesCredentialsEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("SearchEngines credentials access by {$user?->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppSearchEnginesUsername(),
            'password' => $this->envUtils()->getAppSearchEnginesPassword(),
        ];
    }
}
