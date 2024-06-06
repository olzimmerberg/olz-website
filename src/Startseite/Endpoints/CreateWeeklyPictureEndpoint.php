<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Startseite\WeeklyPicture;

class CreateWeeklyPictureEndpoint extends OlzCreateEntityEndpoint {
    use WeeklyPictureEndpointTrait;

    public static function getIdent(): string {
        return 'CreateWeeklyPictureEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('weekly_picture');

        $entity = new WeeklyPicture();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
