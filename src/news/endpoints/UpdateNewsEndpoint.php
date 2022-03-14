<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../model/Role.php';
require_once __DIR__.'/../../model/User.php';
require_once __DIR__.'/../model/NewsEntry.php';
require_once __DIR__.'/AbstractNewsEndpoint.php';

class UpdateNewsEndpoint extends AbstractNewsEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/env/EnvUtils.php';
        require_once __DIR__.'/../../utils/UploadUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $upload_utils = UploadUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setDateUtils($_DATE);
        $this->setEntityManager($entityManager);
        $this->setEnvUtils($env_utils);
        $this->setUploadUtils($upload_utils);
    }

    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function setUploadUtils($uploadUtils) {
        $this->uploadUtils = $uploadUtils;
    }

    public static function getIdent() {
        return 'UpdateNewsEndpoint';
    }

    public function getResponseField() {
        $news_data_field = self::getNewsDataField();
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => new FieldTypes\IntegerField(['allow_null' => false, 'min_value' => 1]),
            // 'data' => $news_data_field,
        ]]);
    }

    public function getRequestField() {
        $news_data_field = self::getNewsDataField();
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField(['allow_null' => false, 'min_value' => 1]),
            'data' => $news_data_field,
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('news');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user_repo = $this->entityManager->getRepository(User::class);
        $role_repo = $this->entityManager->getRepository(Role::class);
        $current_user = $this->authUtils->getSessionUser();

        $owner_user_id = $input['data']['ownerUserId'] ?? null;
        $owner_user = $current_user;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
        }

        $owner_role_id = $input['data']['ownerRoleId'] ?? null;
        $owner_role = null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
        }

        $author_user_id = $input['data']['authorUserId'] ?? null;
        $author_user = $current_user;
        if ($author_user_id) {
            $author_user = $user_repo->findOneBy(['id' => $author_user_id]);
        }

        $author_role_id = $input['data']['authorRoleId'] ?? null;
        $author_role = null;
        if ($author_role_id) {
            $author_role = $role_repo->findOneBy(['id' => $author_role_id]);
        }

        $today = new DateTime($this->dateUtils->getIsoToday());
        $now = new DateTime($this->dateUtils->getIsoNow());

        $tags_for_db = $this->getTagsForDb($input['data']['tags']);

        $valid_image_ids = $this->uploadUtils->getValidUploadIds($input['data']['imageIds']);

        $entity_id = $input['id'];
        $news_repo = $this->entityManager->getRepository(NewsEntry::class);
        $news_entry = $news_repo->findOneBy(['id' => $entity_id]);

        $news_entry->setCreatedAt($now);
        $news_entry->setLastModifiedAt($now);
        $news_entry->setOwnerUser($owner_user);
        $news_entry->setOwnerRole($owner_role);
        $news_entry->setAuthor($input['data']['author']);
        $news_entry->setAuthorUser($author_user);
        $news_entry->setAuthorRole($author_role);
        $news_entry->setDate($today);
        $news_entry->setTitle($input['data']['title']);
        $news_entry->setTeaser($input['data']['teaser']);
        $news_entry->setContent($input['data']['content']);
        $news_entry->setExternalUrl($input['data']['externalUrl']);
        $news_entry->setTags($tags_for_db);
        $news_entry->setImageIds($valid_image_ids);
        // TODO: Do not ignore
        $news_entry->setTermin(0);
        $news_entry->setOnOff($input['data']['onOff'] ? 1 : 0);
        $news_entry->setCounter(0);
        $news_entry->setType('aktuell');
        $news_entry->setNewsletter(1);

        $this->entityManager->persist($news_entry);
        $this->entityManager->flush();

        $news_entry_id = $news_entry->getId();

        $news_entry_img_path = "{$data_path}img/news/{$news_entry_id}/";
        $this->uploadUtils->moveUploads($valid_image_ids, "{$news_entry_img_path}img/");
        // TODO: Generate default thumbnails.

        $news_entry_files_path = "{$data_path}files/news/{$news_entry_id}/";
        $this->uploadUtils->moveUploads($input['data']['fileIds'], $news_entry_files_path);

        return [
            'status' => 'OK',
            'id' => $news_entry_id,
        ];
    }
}
