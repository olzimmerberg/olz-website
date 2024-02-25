<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditKarteEndpoint extends OlzEditEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'EditKarteEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $karte = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($karte, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($karte);

        return [
            'id' => $karte->getId(),
            'meta' => $karte->getMetaData(),
            'data' => $this->getEntityData($karte),
        ];
    }
}
