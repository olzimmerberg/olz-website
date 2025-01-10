<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminLocationId from TerminLocationEndpointTrait
 * @phpstan-import-type OlzTerminLocationData from TerminLocationEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzTerminLocationId, OlzTerminLocationData>
 */
class UpdateTerminLocationEndpoint extends OlzUpdateEntityTypedEndpoint {
    use TerminLocationEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(TerminLocationEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity);

        return [
            'id' => $entity->getId(),
        ];
    }
}
