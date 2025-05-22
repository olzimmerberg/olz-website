<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzLinkId from LinkEndpointTrait
 * @phpstan-import-type OlzLinkData from LinkEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzLinkId, OlzLinkData>
 */
class EditLinkEndpoint extends OlzEditEntityTypedEndpoint {
    use EntityUtilsTrait;
    use LinkEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(LinkEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
