<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzDownloadId from DownloadEndpointTrait
 * @phpstan-import-type OlzDownloadData from DownloadEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzDownloadId, OlzDownloadData>
 */
class DeleteDownloadEndpoint extends OlzDeleteEntityTypedEndpoint {
    use DownloadEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
