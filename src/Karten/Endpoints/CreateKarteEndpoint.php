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

        $entity = new Karte();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
