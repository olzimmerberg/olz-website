<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzUserId from UserEndpointTrait
 * @phpstan-import-type OlzUserData from UserEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzUserId, OlzUserData>
 */
class GetUserEndpoint extends OlzGetEntityTypedEndpoint {
    use UserEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureUserEndpointTrait();
        $this->phpStanUtils->registerTypeImport(UserEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('users');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
