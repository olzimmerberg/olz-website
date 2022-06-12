<?php

namespace Olz\Api;

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/OlzEndpoint.php';

abstract class OlzEntityEndpoint extends OlzEndpoint {
    use \Psr\Log\LoggerAwareTrait;

    abstract public function usesExternalId(): bool;

    abstract public function getEntityDataField(bool $allow_null): FieldTypes\Field;

    protected function getIdField() {
        if ($this->usesExternalId()) {
            return new FieldTypes\StringField(['allow_null' => false]);
        }
        return new FieldTypes\IntegerField(['allow_null' => false, 'min_value' => 1]);
    }
}
