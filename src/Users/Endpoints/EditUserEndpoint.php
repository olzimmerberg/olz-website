<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzUserId from UserEndpointTrait
 * @phpstan-import-type OlzUserData from UserEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzUserId, OlzUserData>
 */
class EditUserEndpoint extends OlzEditEntityTypedEndpoint {
    use UserEndpointTrait;

    protected function handle(mixed $input): mixed {
        $entity = $this->getEntityById($input['id']);

        $current_user = $this->authUtils()->getCurrentUser();
        $is_me = (
            $current_user
            && $entity->getUsername() === $current_user->getUsername()
            && $entity->getId() === $current_user->getId()
        );
        $can_update = $this->entityUtils()->canUpdateOlzEntity($entity, null, 'users');
        if (!$is_me && !$can_update) {
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
