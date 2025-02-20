<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzDownloadId from DownloadEndpointTrait
 * @phpstan-import-type OlzDownloadData from DownloadEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzDownloadId, OlzDownloadData>
 */
class UpdateDownloadEndpoint extends OlzUpdateEntityTypedEndpoint {
    use DownloadEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(DownloadEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId(),
        ];
    }
}
