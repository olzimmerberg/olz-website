<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzWeeklyPictureId from WeeklyPictureEndpointTrait
 * @phpstan-import-type OlzWeeklyPictureData from WeeklyPictureEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzWeeklyPictureId, OlzWeeklyPictureData>
 */
class GetWeeklyPictureEndpoint extends OlzGetEntityTypedEndpoint {
    use WeeklyPictureEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureWeeklyPictureEndpointTrait();
        $this->phpStanUtils->registerTypeImport(WeeklyPictureEndpointTrait::class);
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
