<?php

namespace Olz\Apps\Import\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class ImportTermineEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'ImportTermineEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => []]);
    }

    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [];
    }
}
