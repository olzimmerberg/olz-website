<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteKarteEndpoint extends OlzDeleteEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'DeleteKarteEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $karte = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($karte, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $karte->setOnOff(0);
        $this->entityManager()->persist($karte);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
