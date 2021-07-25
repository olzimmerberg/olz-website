<?php

require_once __DIR__.'/../../api/common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/DictField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/Role.php';
require_once __DIR__.'/../../model/User.php';
require_once __DIR__.'/../model/NewsEntry.php';

class CreateNewsEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'CreateNewsEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'newsId' => new IntegerField(['allow_null' => true, 'min_value' => 1]),
        ];
    }

    public function getRequestFields() {
        return [
            'ownerUserId' => new IntegerField(['allow_null' => true, 'min_value' => 1]),
            'ownerRoleId' => new IntegerField(['allow_null' => true, 'min_value' => 1]),
            'author' => new StringField(['allow_null' => true]),
            'authorUserId' => new IntegerField(['allow_null' => true, 'min_value' => 1]),
            'authorRoleId' => new IntegerField(['allow_null' => true, 'min_value' => 1]),
            'title' => new StringField([]),
            'teaser' => new StringField(['allow_empty' => true]),
            'content' => new StringField(['allow_empty' => true]),
            'external_url' => new StringField(['allow_null' => true]),
            'tags' => new ArrayField([
                'item_field' => new StringField([]),
            ]),
            'terminId' => new IntegerField(['allow_null' => true, 'min_value' => 1]),
            'onOff' => new BooleanField(['default_value' => true]),
            'imageIds' => new ArrayField([
                'item_field' => new StringField([]),
            ]),
            'fileIds' => new ArrayField([
                'item_field' => new StringField([]),
            ]),
        ];
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('news');
        if (!$has_access) {
            return ['status' => 'ERROR'];
        }

        $user_repo = $this->entityManager->getRepository(User::class);
        $role_repo = $this->entityManager->getRepository(Role::class);
        $current_user = $this->authUtils->getSessionUser();

        $owner_user_id = $input['ownerUserId'] ?? null;
        $owner_user = $current_user;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        $owner_role = null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
        }

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

        $tags_for_db = ' '.implode(' ', $input['tags']).' ';

        $news_entry = new NewsEntry();
        $news_entry->setOwnerUser($owner_user);
        $news_entry->setOwnerRole($owner_role);
        $news_entry->setAuthor($input['author']);
        $news_entry->setAuthorUser($author_user);
        $news_entry->setAuthorRole($author_role);
        $news_entry->setTitle($input['title']);
        $news_entry->setTeaser($input['teaser']);
        $news_entry->setContent($input['content']);
        $news_entry->setExternalUrl($input['external_url']);
        $news_entry->setTags($tags_for_db);
        // TODO: Do not ignore
        $news_entry->setTermin(null);
        $news_entry->setOnOff($input['onOff'] ? 1 : 0);

        $this->entityManager->persist($news_entry);
        $this->entityManager->flush();
        return [
            'status' => 'OK',
            'newsId' => $news_entry->getId(),
        ];
    }
}
