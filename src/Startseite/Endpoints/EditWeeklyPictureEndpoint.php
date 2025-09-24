<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzWeeklyPictureId from WeeklyPictureEndpointTrait
 * @phpstan-import-type OlzWeeklyPictureData from WeeklyPictureEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzWeeklyPictureId, OlzWeeklyPictureData>
 */
class EditWeeklyPictureEndpoint extends OlzEditEntityTypedEndpoint {
    use WeeklyPictureEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'weekly_picture')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
