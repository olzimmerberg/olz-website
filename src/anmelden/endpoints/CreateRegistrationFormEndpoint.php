<?php

require_once __DIR__.'/../../api/common/Endpoint.php';
require_once __DIR__.'/../../fields/ArrayField.php';
require_once __DIR__.'/../../fields/BooleanField.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/ObjectField.php';
require_once __DIR__.'/../../fields/StringField.php';
require_once __DIR__.'/../constants.php';

class CreateRegistrationFormEndpoint extends Endpoint {
    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'CreateRegistrationFormEndpoint';
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
            new StringField('title', ['allow_empty' => false]),
            new StringField('description', ['allow_empty' => true]),
            // see README for documentation.
            new ArrayField('fields', [
                'item_field' => new ObjectField('item', [
                    'field_structure' => [
                        'type' => new EnumField('type', [
                            'allowed_values' => VALID_FIELD_TYPES,
                        ]),
                        'isOptional' => new BooleanField('isOptional'),
                        'title' => new StringField('title', ['allow_empty' => false]),
                        'description' => new StringField('description', ['allow_empty' => true]),
                        'options' => new ArrayField('fields', [
                            'item_field' => new StringField('item'),
                            'allow_null' => true,
                        ]),
                    ],
                ]),
            ]),
            new DateTimeField('opensAt', ['allow_null' => true]),
            new DateTimeField('closesAt', ['allow_null' => true]),
            new IntegerField('ownerUser', ['allow_empty' => false]),
            new IntegerField('ownerRole', ['allow_empty' => true]),
        ];
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
