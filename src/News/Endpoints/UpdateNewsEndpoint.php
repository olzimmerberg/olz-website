<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\News\NewsEntry;
use PhpTypeScriptApi\HttpError;

class UpdateNewsEndpoint extends OlzUpdateEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'UpdateNewsEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $input['id']]);

        if (!$news_entry) {
            throw new HttpError(404, "Nicht gefunden.");
        }
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
