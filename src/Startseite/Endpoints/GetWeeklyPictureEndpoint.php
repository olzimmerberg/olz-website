<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetWeeklyPictureEndpoint extends OlzGetEntityEndpoint {
    use WeeklyPictureEndpointTrait;

    public static function getIdent(): string {
        return 'GetWeeklyPictureEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
