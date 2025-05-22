<?php

namespace Olz\Apps\Youtube\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\EnvUtilsTrait;
use Olz\Utils\LogTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{},
 *   array{username: string, password: string}
 * >
 */
class GetAppYoutubeCredentialsEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use EnvUtilsTrait;
    use LogTrait;

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Youtube credentials access by {$user?->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppSearchEnginesUsername(),
            'password' => $this->envUtils()->getAppSearchEnginesPassword(),
        ];
    }
}
