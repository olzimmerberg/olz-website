<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Karten\Karte;
use PhpTypeScriptApi\HttpError;

class UpdateKarteEndpoint extends OlzUpdateEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'UpdateKarteEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karte = $karten_repo->findOneBy(['id' => $input['id']]);

        if (!$karte) {
            throw new HttpError(404, "Nicht gefunden.");
        }
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
