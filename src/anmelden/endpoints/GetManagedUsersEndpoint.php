<?php

require_once __DIR__.'/../../api/common/Endpoint.php';
require_once __DIR__.'/../../fields/ArrayField.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/ObjectField.php';
require_once __DIR__.'/../../fields/StringField.php';

class GetManagedUsersEndpoint extends Endpoint {
    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'GetManagedUsersEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            new ArrayField('managedUsers', [
                'item_field' => new ObjectField('item', [
                    'field_structure' => [
                        'id' => new IntegerField('id', []),
                        'firstName' => new IntegerField('firstName', []),
                        'lastName' => new IntegerField('lastName', []),
                    ],
                ]),
                'allow_null' => true,
            ]),
        ];
    }

    public function getRequestFields() {
        return [];
    }

    protected function handle($input) {
        // TODO: Implement
        return [
            'status' => 'ERROR',
        ];
    }
}
