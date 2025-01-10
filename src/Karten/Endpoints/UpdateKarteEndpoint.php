<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzKarteId from KarteEndpointTrait
 * @phpstan-import-type OlzKarteData from KarteEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzKarteKind from KarteEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzKarteId, OlzKarteData>
 */
class UpdateKarteEndpoint extends OlzUpdateEntityTypedEndpoint {
    use KarteEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(KarteEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId(),
        ];
    }
}
