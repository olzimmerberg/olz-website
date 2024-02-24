<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetLinkEndpoint extends OlzGetEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'GetLinkEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $link = $this->getEntityById($input['id']);

        return [
            'id' => $link->getId(),
            'meta' => $link->getMetaData(),
            'data' => $this->getEntityData($link),
        ];
    }
}
