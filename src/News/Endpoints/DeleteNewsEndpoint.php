<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzNewsId from NewsEndpointTrait
 * @phpstan-import-type OlzNewsData from NewsEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzNewsFormat from NewsEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzNewsId, OlzNewsData>
 */
class DeleteNewsEndpoint extends OlzDeleteEntityTypedEndpoint {
    use NewsEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
