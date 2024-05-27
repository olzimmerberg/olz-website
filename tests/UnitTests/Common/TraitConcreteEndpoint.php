<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common;

use PhpTypeScriptApi\Fields\FieldTypes;

trait TraitConcreteEndpoint {
    public static function getIdent(): string {
        return 'ident';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\Field();
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\Field();
    }

    protected function handle($input) {
    }
}
