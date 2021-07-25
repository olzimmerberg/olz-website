<?php

require_once __DIR__.'/../../api/common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/DictField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../constants.php';

class GetRegistrationFormEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'GetRegistrationFormEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'title' => new StringField(['allow_empty' => false]),
            'description' => new StringField(['allow_empty' => true]),
            // see README for documentation.
            'fields' => new ArrayField([
                'item_field' => new ObjectField([
                    'field_structure' => [
                        'type' => new EnumField([
                            'allowed_values' => VALID_FIELD_TYPES,
                        ]),
                        'isOptional' => new BooleanField(),
                        'title' => new StringField(['allow_empty' => false]),
                        'description' => new StringField(['allow_empty' => true]),
                        'options' => new ArrayField([
                            'item_field' => new StringField(),
                            'allow_null' => true,
                        ]),
                    ],
                ]),
            ]),
            'opensAt' => new DateTimeField(['allow_null' => true]),
            'closesAt' => new DateTimeField(['allow_null' => true]),
            'ownerUser' => new IntegerField(['allow_empty' => false]),
            'ownerRole' => new IntegerField(['allow_empty' => true]),
            'prefillValues' => new DictField(['item_field' => new Field(), 'allow_null' => true]),
        ];
    }

    public function getRequestFields() {
        return [
            'registrationForm' => new IntegerField(['min_value' => 1]),
            'user' => new IntegerField(['min_value' => 1]), // Can be a managed user
        ];
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
