<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Karten\Karte;
use PhpTypeScriptApi\HttpError;

class GetKarteEndpoint extends OlzGetEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'GetKarteEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karte = $karten_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $karte->getId(),
            'meta' => $karte->getMetaData(),
            'data' => $this->getEntityData($karte),
        ];
    }
}
