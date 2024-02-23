<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Service\Link;

class CreateLinkEndpoint extends OlzCreateEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'CreateLinkEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('links');

        $link = new Link();
        $this->entityUtils()->createOlzEntity($link, $input['meta']);
        $this->updateEntityWithData($link, $input['data']);

        $this->entityManager()->persist($link);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
            'id' => $link->getId(),
        ];
    }
}
