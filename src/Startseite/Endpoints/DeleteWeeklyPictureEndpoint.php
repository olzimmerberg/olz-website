<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzWeeklyPictureId from WeeklyPictureEndpointTrait
 * @phpstan-import-type OlzWeeklyPictureData from WeeklyPictureEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzWeeklyPictureId, OlzWeeklyPictureData>
 */
class DeleteWeeklyPictureEndpoint extends OlzDeleteEntityTypedEndpoint {
    use WeeklyPictureEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureWeeklyPictureEndpointTrait();
        $this->phpStanUtils->registerTypeImport(WeeklyPictureEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'weekly_picture')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity->setOnOff(0);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
