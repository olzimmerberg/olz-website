<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Service\Download;

/**
 * @phpstan-import-type OlzDownloadId from DownloadEndpointTrait
 * @phpstan-import-type OlzDownloadData from DownloadEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzDownloadId, OlzDownloadData>
 */
class CreateDownloadEndpoint extends OlzCreateEntityTypedEndpoint {
    use DownloadEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(DownloadEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('downloads');

        $entity = new Download();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId(),
        ];
    }
}
