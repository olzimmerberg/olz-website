<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditWeeklyPictureEndpoint extends OlzEditEntityEndpoint {
    use WeeklyPictureEndpointTrait;

    public static function getIdent(): string {
        return 'EditWeeklyPictureEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'weekly_picture')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($entity);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
