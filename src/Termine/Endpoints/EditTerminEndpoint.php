<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class EditTerminEndpoint extends OlzEditEntityTypedEndpoint {
    use EntityUtilsTrait;
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

        $this->editUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
