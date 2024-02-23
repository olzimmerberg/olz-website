<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditLinkEndpoint extends OlzEditEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'EditLinkEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $link = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($link, null, 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $link->getId(),
            'meta' => $link->getMetaData(),
            'data' => $this->getEntityData($link),
        ];
    }
}
