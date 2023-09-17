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
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $entity_id]);

        if (!$news_entry) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($news_entry, null, 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $owner_user = $news_entry->getOwnerUser();
        $owner_role = $news_entry->getOwnerRole();

        $image_ids = $news_entry->getImageIds();
        $news_entry_img_path = "{$data_path}img/news/{$entity_id}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$news_entry_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $news_entry_files_path = "{$data_path}files/news/{$entity_id}/";
        if (!is_dir("{$news_entry_files_path}")) {
            mkdir("{$news_entry_files_path}", 0777, true);
        }
        $files_path_entries = scandir($news_entry_files_path);
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_path = "{$news_entry_files_path}{$file_id}";
                $temp_path = "{$data_path}temp/{$file_id}";
                copy($file_path, $temp_path);
            }
        }

        return [
            'id' => $news_entry->getId(),
            'meta' => $news_entry->getMetaData(),
            'data' => $this->getEntityData($news_entry),
        ];
    }
}
