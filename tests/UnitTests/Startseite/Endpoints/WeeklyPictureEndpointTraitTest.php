<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Startseite\Endpoints\WeeklyPictureEndpointTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class WeeklyPictureEndpointTraitConcreteEndpoint {
    use WeeklyPictureEndpointTrait;

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

/**
 * @internal
 *
 * @covers \Olz\Startseite\Endpoints\WeeklyPictureEndpointTrait
 */
final class WeeklyPictureEndpointTraitTest extends UnitTestCase {
    public function testWeeklyPictureEndpointTrait(): void {
        $endpoint = new WeeklyPictureEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'alternativeImageId',
            'imageId',
            'text',
        ], $keys);
    }
}
