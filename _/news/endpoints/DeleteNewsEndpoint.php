<?php

use Olz\Entity\News\NewsEntry;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../api/OlzDeleteEntityEndpoint.php';
require_once __DIR__.'/NewsEndpointTrait.php';

class DeleteNewsEndpoint extends OlzDeleteEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'DeleteNewsEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('news');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $news_repo = $this->entityManager->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $entity_id]);

        if ($news_entry) {
            $this->entityManager->remove($news_entry);
            $this->entityManager->flush();
            return ['status' => 'OK'];
        }
        return ['status' => 'ERROR'];
    }
}
