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
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
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
        ];
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
