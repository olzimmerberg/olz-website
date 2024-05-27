<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteNewsEndpoint extends OlzDeleteEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent(): string {
        return 'DeleteNewsEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $news_entry = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($news_entry, null, 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $news_entry->setOnOff(0);
        $this->entityManager()->persist($news_entry);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
