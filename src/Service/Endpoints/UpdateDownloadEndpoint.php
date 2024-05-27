<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateDownloadEndpoint extends OlzUpdateEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateDownloadEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
