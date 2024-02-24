<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Karten\Karte;

class CreateKarteEndpoint extends OlzCreateEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'CreateKarteEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('karten');

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
