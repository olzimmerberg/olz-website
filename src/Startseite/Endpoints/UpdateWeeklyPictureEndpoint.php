<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateWeeklyPictureEndpoint extends OlzUpdateEntityEndpoint {
    use WeeklyPictureEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateWeeklyPictureEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'weekly_picture')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
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
