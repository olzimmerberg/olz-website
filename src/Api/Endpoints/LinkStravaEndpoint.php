<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     code: non-empty-string,
 *   },
 *   array{}
 * >
 */
class LinkStravaEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $strava_link = $this->stravaUtils()->linkStrava($input['code']);
        if ($strava_link === null) {
            throw new HttpError(400, "Ungültige Anfrage!");
        }

        return [];
    }
}
