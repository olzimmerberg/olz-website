<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../constants.php';

class GetRegistrationFormEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        $this->setEntityManager($entityManager);
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'GetRegistrationFormEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'title' => new FieldTypes\StringField(['allow_empty' => false]),
            'description' => new FieldTypes\StringField(['allow_empty' => true]),
            // see README for documentation.
            'fields' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\ObjectField([
                    'field_structure' => [
                        'type' => new FieldTypes\EnumField([
                            'allowed_values' => VALID_FIELD_TYPES,
                        ]),
                        'isOptional' => new FieldTypes\BooleanField(),
                        'title' => new FieldTypes\StringField(['allow_empty' => false]),
                        'description' => new FieldTypes\StringField(['allow_empty' => true]),
                        'options' => new FieldTypes\ArrayField([
                            'item_field' => new FieldTypes\StringField(),
                            'allow_null' => true,
                        ]),
                    ],
                ]),
            ]),
            'opensAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'closesAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'ownerUser' => new FieldTypes\IntegerField(['allow_empty' => false]),
            'ownerRole' => new FieldTypes\IntegerField(['allow_empty' => true]),
            'prefillValues' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\Field(),
                'allow_null' => true,
            ]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'registrationForm' => new FieldTypes\IntegerField(['min_value' => 1]),
            'user' => new FieldTypes\IntegerField(['min_value' => 1]), // Can be a managed user
        ]]);
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
