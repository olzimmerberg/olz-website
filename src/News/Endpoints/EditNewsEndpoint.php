<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditNewsEndpoint extends OlzEditEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'EditNewsEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $news_entry = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($news_entry, null, 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_ids = $news_entry->getImageIds();
        $news_entry_img_path = "{$data_path}img/news/{$news_entry->getId()}/";
        foreach ($image_ids ?? [] as $image_id) {
            $image_path = "{$news_entry_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        $news_entry_files_path = "{$data_path}files/news/{$news_entry->getId()}/";
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
