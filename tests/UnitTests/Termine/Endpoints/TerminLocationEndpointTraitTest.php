<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Termine\Endpoints\TerminLocationEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class TerminLocationEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use TerminLocationEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\TerminLocationEndpointTrait
 */
final class TerminLocationEndpointTraitTest extends UnitTestCase {
    public function testTerminLocationEndpointTrait(): void {
        $endpoint = new TerminLocationEndpointTraitConcreteEndpoint();
        $this->assertFalse($endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertTrue($field instanceof FieldTypes\ObjectField);
        $this->assertFalse($field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'details',
            'imageIds',
            'latitude',
            'longitude',
            'name',
        ], $keys);
    }
}
