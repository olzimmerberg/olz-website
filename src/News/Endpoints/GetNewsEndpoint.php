<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\News\NewsEntry;
use PhpTypeScriptApi\HttpError;

class GetNewsEndpoint extends OlzGetEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'GetNewsEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $news_entry->getId(),
            'meta' => $news_entry->getMetaData(),
            'data' => $this->getEntityData($news_entry),
        ];
    }
}
