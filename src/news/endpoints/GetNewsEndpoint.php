<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../model/Role.php';
require_once __DIR__.'/../../model/User.php';
require_once __DIR__.'/../model/NewsEntry.php';
require_once __DIR__.'/AbstractNewsEndpoint.php';

class GetNewsEndpoint extends AbstractNewsEndpoint {
    public static function getIdent() {
        return 'GetNewsEndpoint';
    }

    public function getResponseField() {
        $news_data_field = self::getNewsDataField();
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField(['allow_null' => false, 'min_value' => 1]),
            'data' => $news_data_field,
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField(['allow_null' => false, 'min_value' => 1]),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('news');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $news_repo = $this->entityManager->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $entity_id]);

        $owner_user = $news_entry->getOwnerUser();
        $owner_role = $news_entry->getOwnerRole();
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $tags_for_api = array_filter(
            explode(' ', trim($news_entry->getTags())),
            function ($item) {
                return trim($item) != '';
            }
        );
        $termin_id = $news_entry->getTermin();

        return [
            'id' => $entity_id,
            'data' => [
                'ownerUserId' => $owner_user ? $owner_user->getId() : null,
                'ownerRoleId' => $owner_role ? $owner_role->getId() : null,
                'author' => $news_entry->getAuthor(),
                'authorUserId' => $author_user ? $author_user->getId() : null,
                'authorRoleId' => $author_role ? $author_role->getId() : null,
                'title' => $news_entry->getTitle(),
                'teaser' => $news_entry->getTeaser(),
                'content' => $news_entry->getContent(),
                'externalUrl' => $news_entry->getExternalUrl(),
                'tags' => $tags_for_api,
                'terminId' => $termin_id ? $termin_id : null,
                'onOff' => $news_entry->getOnOff() ? true : false,
                'imageIds' => $news_entry->getImageIds(),
                'fileIds' => [], // $news_entry->getFileIds(),
            ],
        ];
    }
}
