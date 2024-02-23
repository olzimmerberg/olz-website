<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetDownloadEndpoint extends OlzGetEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'GetDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $download = $this->getEntityById($input['id']);

        return [
            'id' => $download->getId(),
            'meta' => $download->getMetaData(),
            'data' => $this->getEntityData($download),
        ];
    }
}
