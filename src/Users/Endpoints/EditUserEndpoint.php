<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditUserEndpoint extends OlzEditEntityEndpoint {
    use UserEndpointTrait;

    public static function getIdent(): string {
        return 'EditUserEndpoint';
    }

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
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
