<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzLinkId from LinkEndpointTrait
 * @phpstan-import-type OlzLinkData from LinkEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzLinkId, OlzLinkData>
 */
class UpdateLinkEndpoint extends OlzUpdateEntityTypedEndpoint {
    use LinkEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(LinkEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId(),
        ];
    }
}
