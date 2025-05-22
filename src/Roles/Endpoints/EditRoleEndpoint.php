<?php

namespace Olz\Roles\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzRoleId from RoleEndpointTrait
 * @phpstan-import-type OlzRoleData from RoleEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzRoleId, OlzRoleData>
 */
class EditRoleEndpoint extends OlzEditEntityTypedEndpoint {
    use AuthUtilsTrait;
    use EntityUtilsTrait;
    use RoleEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(RoleEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $entity = $this->getEntityById($input['id']);

        $is_superior = $this->authUtils()->hasRoleEditPermission($input['id']);
        $is_owner = $this->entityUtils()->canUpdateOlzEntity($entity, null, 'roles');
        if (!$is_superior && !$is_owner) {
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
