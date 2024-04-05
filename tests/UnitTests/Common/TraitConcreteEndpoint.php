<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common;

use PhpTypeScriptApi\Fields\FieldTypes;

trait TraitConcreteEndpoint {
    public static function getIdent() {
        return 'ident';
    }

    public function getResponseField() {
        return new FieldTypes\Field();
    }

    public function getRequestField() {
        return new FieldTypes\Field();
    }

    protected function handle($input) {
    }
}
