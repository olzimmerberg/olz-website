<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class DeleteTerminEndpoint extends OlzDeleteEntityTypedEndpoint {
    use TerminEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureTerminEndpointTrait();
        $this->phpStanUtils->registerTypeImport(TerminEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity->setOnOff(0);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
