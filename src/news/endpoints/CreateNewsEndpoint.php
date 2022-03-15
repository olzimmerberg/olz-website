<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../model/Role.php';
require_once __DIR__.'/../../model/User.php';
require_once __DIR__.'/../model/NewsEntry.php';
require_once __DIR__.'/AbstractNewsEndpoint.php';

class CreateNewsEndpoint extends AbstractNewsEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/env/EnvUtils.php';
        require_once __DIR__.'/../../utils/EntityUtils.php';
        require_once __DIR__.'/../../utils/UploadUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $entity_utils = EntityUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $upload_utils = UploadUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setDateUtils($_DATE);
        $this->setEntityManager($entityManager);
        $this->setEntityUtils($entity_utils);
        $this->setEnvUtils($env_utils);
        $this->setUploadUtils($upload_utils);
    }

    public function setAuthUtils($authUtils) {
        $this->authUtils = $authUtils;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setEntityUtils($entityUtils) {
        $this->entityUtils = $entityUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function setUploadUtils($uploadUtils) {
        $this->uploadUtils = $uploadUtils;
    }

    public static function getIdent() {
        return 'CreateNewsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'newsId' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
        ]]);
    }

    public function getRequestField() {
        return self::getNewsDataField();
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

        $author_user_id = $input['authorUserId'] ?? null;
        $author_user = $current_user;
        if ($author_user_id) {
            $author_user = $user_repo->findOneBy(['id' => $author_user_id]);
        }

        $author_role_id = $input['authorRoleId'] ?? null;
        $author_role = null;
        if ($author_role_id) {
            $author_role = $role_repo->findOneBy(['id' => $author_role_id]);
        }

        $today = new DateTime($this->dateUtils->getIsoToday());

        $tags_for_db = $this->getTagsForDb($input['tags']);

        $valid_image_ids = $this->uploadUtils->getValidUploadIds($input['imageIds']);

        $news_entry = new NewsEntry();
        $this->entityUtils->createOlzEntity($news_entry, $input);
        $news_entry->setAuthor($input['author']);
        $news_entry->setAuthorUser($author_user);
        $news_entry->setAuthorRole($author_role);
        $news_entry->setDate($today);
        $news_entry->setTitle($input['title']);
        $news_entry->setTeaser($input['teaser']);
        $news_entry->setContent($input['content']);
        $news_entry->setExternalUrl($input['externalUrl']);
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
        $this->uploadUtils->moveUploads($input['fileIds'], $news_entry_files_path);

        return [
            'status' => 'OK',
            'newsId' => $news_entry_id,
        ];
    }
}
