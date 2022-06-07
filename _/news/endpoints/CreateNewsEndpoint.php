<?php

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\User;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../api/OlzCreateEntityEndpoint.php';
require_once __DIR__.'/NewsEndpointTrait.php';

class CreateNewsEndpoint extends OlzCreateEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'CreateNewsEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('news');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user_repo = $this->entityManager->getRepository(User::class);
        $role_repo = $this->entityManager->getRepository(Role::class);
        $current_user = $this->authUtils->getSessionUser();
        $data_path = $this->envUtils->getDataPath();
        $input_data = $input['data'];

        $author_user_id = $input_data['authorUserId'] ?? null;
        $author_user = $current_user;
        if ($author_user_id) {
            $author_user = $user_repo->findOneBy(['id' => $author_user_id]);
        }

        $author_role_id = $input_data['authorRoleId'] ?? null;
        $author_role = null;
        if ($author_role_id) {
            $author_role = $role_repo->findOneBy(['id' => $author_role_id]);
        }

        $today = new \DateTime($this->dateUtils->getIsoToday());

        $tags_for_db = $this->getTagsForDb($input_data['tags']);

        $valid_image_ids = $this->uploadUtils->getValidUploadIds($input_data['imageIds']);

        $news_entry = new NewsEntry();
        $this->entityUtils->createOlzEntity($news_entry, $input['meta']);
        $news_entry->setAuthor($input_data['author']);
        $news_entry->setAuthorUser($author_user);
        $news_entry->setAuthorRole($author_role);
        $news_entry->setDate($today);
        $news_entry->setTitle($input_data['title']);
        $news_entry->setTeaser($input_data['teaser']);
        $news_entry->setContent($input_data['content']);
        $news_entry->setExternalUrl($input_data['externalUrl']);
        $news_entry->setTags($tags_for_db);
        $news_entry->setImageIds($valid_image_ids);
        // TODO: Do not ignore
        $news_entry->setTermin(0);
        $news_entry->setCounter(0);
        $news_entry->setType('aktuell');
        $news_entry->setNewsletter(1);

        $this->entityManager->persist($news_entry);
        $this->entityManager->flush();

        $news_entry_id = $news_entry->getId();

        $news_entry_img_path = "{$data_path}img/news/{$news_entry_id}/";
        mkdir("{$news_entry_img_path}img/", 0777, true);
        mkdir("{$news_entry_img_path}thumb/", 0777, true);
        $this->uploadUtils->moveUploads($valid_image_ids, "{$news_entry_img_path}img/");
        // TODO: Generate default thumbnails.

        $news_entry_files_path = "{$data_path}files/news/{$news_entry_id}/";
        $this->uploadUtils->moveUploads($input_data['fileIds'], $news_entry_files_path);

        return [
            'status' => 'OK',
            'id' => $news_entry_id,
        ];
    }
}
