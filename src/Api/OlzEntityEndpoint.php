<?php

namespace Olz\Api;

use PhpTypeScriptApi\Fields\FieldTypes;

abstract class OlzEntityEndpoint extends OlzEndpoint {
    use \Psr\Log\LoggerAwareTrait;
    use OlzEntityEndpointTrait;

    abstract public function usesExternalId(): bool;

    abstract public function getEntityDataField(bool $allow_null): FieldTypes\Field;

    protected function getIdField(bool $allow_null = false): FieldTypes\Field {
        if ($this->usesExternalId()) {
            return new FieldTypes\StringField([
                'allow_null' => $allow_null,
            ]);
        }
        return new FieldTypes\IntegerField([
            'allow_null' => $allow_null,
            'min_value' => 1,
        ]);
    }
}
