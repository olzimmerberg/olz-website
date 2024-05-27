<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditNewsEndpoint extends OlzEditEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent(): string {
        return 'EditNewsEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $news_entry = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($news_entry, null, 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($news_entry);

        return [
            'id' => $news_entry->getId(),
            'meta' => $news_entry->getMetaData(),
            'data' => $this->getEntityData($news_entry),
        ];
    }
}
