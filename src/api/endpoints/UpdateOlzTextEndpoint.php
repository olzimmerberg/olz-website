<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/OlzText.php';
require_once __DIR__.'/../../model/User.php';

class UpdateOlzTextEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'UpdateOlzTextEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            'id' => new IntegerField([]),
            'text' => new StringField(['allow_empty' => true]),
        ];
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
