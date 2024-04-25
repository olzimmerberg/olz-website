<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Service\Endpoints\LinkEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class LinkEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use LinkEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\LinkEndpointTrait
 */
final class LinkEndpointTraitTest extends UnitTestCase {
    public function testLinkEndpointTrait(): void {
        $endpoint = new LinkEndpointTraitConcreteEndpoint();
        $this->assertFalse($endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertTrue($field instanceof FieldTypes\ObjectField);
        $this->assertFalse($field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'name',
            'position',
            'url',
        ], $keys);
    }
}
