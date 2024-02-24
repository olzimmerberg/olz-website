<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateKarteEndpoint extends OlzUpdateEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'UpdateKarteEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $karte = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($karte, $input['meta'], 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($karte, $input['meta'] ?? []);
        $this->updateEntityWithData($karte, $input['data']);

        $this->entityManager()->persist($karte);
        $this->entityManager()->flush();
        $this->persistUploads($karte, $input['data']);

        return [
            'status' => 'OK',
            'id' => $karte->getId(),
        ];
    }
}
