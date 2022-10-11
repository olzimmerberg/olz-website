<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\OlzEntity;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class EditNewsEndpoint extends OlzEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'EditNewsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('news');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $entity_id = $input['id'];
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $entity_id]);

        $owner_user = $news_entry->getOwnerUser();
        $owner_role = $news_entry->getOwnerRole();
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $tags_for_api = $this->getTagsForApi($news_entry->getTags() ?? '');
        $termin_id = $news_entry->getTermin();

        $image_ids = $news_entry->getImageIds();
        $news_entry_img_path = "{$data_path}img/news/{$entity_id}/";
        foreach ($image_ids as $image_id) {
            $image_path = "{$news_entry_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $file_ids = [];
        $news_entry_files_path = "{$data_path}files/news/{$entity_id}/";
        $files_path_entries = scandir($news_entry_files_path);
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_ids[] = $file_id;
                $file_path = "{$news_entry_files_path}{$file_id}";
                $temp_path = "{$data_path}temp/{$file_id}";
                copy($file_path, $temp_path);
            }
        }

        return [
            'id' => $entity_id,
            'meta' => [
                'ownerUserId' => $owner_user ? $owner_user->getId() : null,
                'ownerRoleId' => $owner_role ? $owner_role->getId() : null,
                'onOff' => $news_entry->getOnOff() ? true : false,
            ],
            'data' => [
                'author' => $news_entry->getAuthor(),
                'authorUserId' => $author_user ? $author_user->getId() : null,
                'authorRoleId' => $author_role ? $author_role->getId() : null,
                'title' => $news_entry->getTitle(),
                'teaser' => $news_entry->getTeaser(),
                'content' => $news_entry->getContent(),
                'externalUrl' => $news_entry->getExternalUrl(),
                'tags' => $tags_for_api,
                'terminId' => $termin_id ? $termin_id : null,
                'imageIds' => $image_ids,
                'fileIds' => $file_ids,
            ],
        ];
    }
}
