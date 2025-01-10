<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzDownloadId from DownloadEndpointTrait
 * @phpstan-import-type OlzDownloadData from DownloadEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzDownloadId, OlzDownloadData>
 */
class GetDownloadEndpoint extends OlzGetEntityTypedEndpoint {
    use DownloadEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(DownloadEndpointTrait::class);
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
