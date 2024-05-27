<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateNewsEndpoint extends OlzUpdateEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateNewsEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $news_entry = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($news_entry, $input['meta'], 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($news_entry, $input['meta'] ?? []);
        $this->updateEntityWithData($news_entry, $input['data']);

        $this->entityManager()->persist($news_entry);
        $this->entityManager()->flush();
        $this->persistUploads($news_entry, $input['data']);

        return [
            'status' => 'OK',
            'id' => $news_entry->getId(),
        ];
    }
}
