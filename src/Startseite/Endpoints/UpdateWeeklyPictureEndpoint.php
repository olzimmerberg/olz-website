<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzWeeklyPictureId from WeeklyPictureEndpointTrait
 * @phpstan-import-type OlzWeeklyPictureData from WeeklyPictureEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzWeeklyPictureId, OlzWeeklyPictureData>
 */
class UpdateWeeklyPictureEndpoint extends OlzUpdateEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use WeeklyPictureEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureWeeklyPictureEndpointTrait();
        $this->phpStanUtils->registerTypeImport(WeeklyPictureEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'weekly_picture')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
