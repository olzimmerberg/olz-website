<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\AnmeldenConstants;
use PhpTypeScriptApi\Fields\FieldTypes;

trait RegistrationEndpointTrait {
    public function usesExternalId(): bool {
        return true;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return self::getRegistrationDataField($allow_null);
    }

    public static function getRegistrationDataField(bool $allow_null = false) {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzRegistrationDataOrNull' : 'OlzRegistrationData',
            'field_structure' => [
                'title' => new FieldTypes\StringField(['allow_empty' => false]),
                'description' => new FieldTypes\StringField(['allow_empty' => true]),
                // see README for documentation.
                'infos' => new FieldTypes\ArrayField([
                    'item_field' => AnmeldenConstants::getRegistrationInfoField(),
                ]),
                'opensAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
                'closesAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }
}
