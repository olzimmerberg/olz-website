<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../../model/OlzText.php';
require_once __DIR__.'/../../model/User.php';

class UpdateOlzTextEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'UpdateOlzTextEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            new IntegerField('id', []),
            new StringField('text', ['allow_empty' => true]),
        ];
    }

    protected function handle($input) {
        $auth_username = $this->session->get('user');
        $id = $input['id'];

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $auth_username]);

        // TODO: Generalize this!
        $zugriff = preg_split('/ /', $user->getZugriff());
        $has_access = in_array('all', $zugriff) || in_array("olz_text_{$id}", $zugriff);
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
