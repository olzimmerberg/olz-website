<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;

/**
 * @phpstan-import-type OlzWeeklyPictureId from WeeklyPictureEndpointTrait
 * @phpstan-import-type OlzWeeklyPictureData from WeeklyPictureEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzWeeklyPictureId, OlzWeeklyPictureData>
 */
class CreateWeeklyPictureEndpoint extends OlzCreateEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use WeeklyPictureEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureWeeklyPictureEndpointTrait();
        $this->phpStanUtils->registerTypeImport(WeeklyPictureEndpointTrait::class);
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
            'id' => $entity->getId() ?? 0,
        ];
    }
}
