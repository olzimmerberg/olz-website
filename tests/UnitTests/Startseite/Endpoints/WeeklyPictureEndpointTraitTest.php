<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Startseite\Endpoints\WeeklyPictureEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class WeeklyPictureEndpointTraitConcreteEndpoint {
    use WeeklyPictureEndpointTrait;
    use TraitConcreteEndpoint;
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
            'imageId',
            'text',
        ], $keys);
    }
}
