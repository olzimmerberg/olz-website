<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';
require_once __DIR__.'/../../model/OlzText.php';
require_once __DIR__.'/../../model/User.php';

class UpdateOlzTextEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setEntityManager($entityManager);
    }

    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'UpdateOlzTextEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField([]),
            'text' => new FieldTypes\StringField(['allow_empty' => true]),
        ]]);
    }

    protected function handle($input) {
        $id = $input['id'];

        $has_access = $this->authUtils->hasPermission("olz_text_{$id}");
        if (!$has_access) {
            return ['status' => 'ERROR'];
        }

        $olz_text_repo = $this->entityManager->getRepository(OlzText::class);
        $olz_text = $olz_text_repo->findOneBy(['id' => $id]);
        if (!$olz_text) {
            $olz_text = new OlzText();
            $olz_text->setId($id);
            $olz_text->setOnOff(1);
            $this->entityManager->persist($olz_text);
        }

        $olz_text->setText($input['text']);
        $this->entityManager->flush();

        return [
            'status' => 'OK',
        ];
    }
}
