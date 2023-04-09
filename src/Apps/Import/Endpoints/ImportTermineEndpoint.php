<?php

namespace Olz\Apps\Import\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class ImportTermineEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'ImportTermineEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    protected function handle($input) {
        if (!$this->authUtils()->hasPermission('termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [];
    }
}
