<?php

namespace Olz\Apps\Youtube\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @extends TypedEndpoint<
 *   array{},
 *   array{username: string, password: string}
 * >
 */
class GetAppYoutubeCredentialsEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [];
    }

    public static function getIdent(): string {
        return 'GetAppYoutubeCredentialsEndpoint';
    }

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('all')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Youtube credentials access by {$user->getUsername()}.");

        return [
            'username' => $this->envUtils()->getAppSearchEnginesUsername(),
            'password' => $this->envUtils()->getAppSearchEnginesPassword(),
        ];
    }
}
