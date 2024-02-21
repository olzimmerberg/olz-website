<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Karten\Karte;
use PhpTypeScriptApi\HttpError;

class CreateKarteEndpoint extends OlzCreateEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'CreateKarteEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('karten');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $karte = new Karte();
        $this->entityUtils()->createOlzEntity($karte, $input['meta']);
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
