<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzKarteId from KarteEndpointTrait
 * @phpstan-import-type OlzKarteData from KarteEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzKarteKind from KarteEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzKarteId, OlzKarteData>
 */
class DeleteKarteEndpoint extends OlzDeleteEntityTypedEndpoint {
    use KarteEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(KarteEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity->setOnOff(0);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
