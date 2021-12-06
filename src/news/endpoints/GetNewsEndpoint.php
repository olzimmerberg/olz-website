<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../model/Role.php';
require_once __DIR__.'/../../model/User.php';
require_once __DIR__.'/../model/NewsEntry.php';
require_once __DIR__.'/AbstractNewsEndpoint.php';

class GetNewsEndpoint extends AbstractNewsEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/env/EnvUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setDateUtils($_DATE);
        $this->setEntityManager($entityManager);
        $this->setEnvUtils($env_utils);
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

        $tags_for_api = explode(' ', trim($news_entry->getTags()));

        return [
            'id' => $entity_id,
            'data' => [
                'ownerUserId' => $news_entry->getOwnerUser()->getId(),
                'ownerRoleId' => $news_entry->getOwnerRole()->getId(),
                'author' => $news_entry->getAuthor(),
                'authorUserId' => $news_entry->getAuthorUser()->getId(),
                'authorRoleId' => $news_entry->getAuthorRole()->getId(),
                'title' => $news_entry->getTitle(),
                'teaser' => $news_entry->getTeaser(),
                'content' => $news_entry->getContent(),
                'externalUrl' => $news_entry->getExternalUrl(),
                'tags' => $tags_for_api,
                'terminId' => $news_entry->getTerminId(),
                'onOff' => $news_entry->getOnOff(),
                'imageIds' => $news_entry->getImageIds(),
                'fileIds' => $news_entry->getFileIds(),
            ],
        ];
    }
}
