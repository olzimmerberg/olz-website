<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\Karten\Karte;
use PhpTypeScriptApi\HttpError;

class DeleteKarteEndpoint extends OlzDeleteEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'DeleteKarteEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karte = $karten_repo->findOneBy(['id' => $entity_id]);

        if (!$karte) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($karte, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $karte->setOnOff(0);
        $this->entityManager()->persist($karte);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
