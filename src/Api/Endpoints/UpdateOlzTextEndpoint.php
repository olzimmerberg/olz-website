<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\OlzText;
use PhpTypeScriptApi\Fields\FieldTypes;

class UpdateOlzTextEndpoint extends OlzEndpoint {
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

        $this->checkPermission("olz_text_{$id}");

        $olz_text_repo = $this->entityManager()->getRepository(OlzText::class);
        $olz_text = $olz_text_repo->findOneBy(['id' => $id]);
        if (!$olz_text) {
            $olz_text = new OlzText();
            $olz_text->setId($id);
            $olz_text->setOnOff(1);
            $this->entityManager()->persist($olz_text);
        }

        $olz_text->setText($input['text']);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
        ];
    }
}
