<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Termine\Endpoints\TerminLabelEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class TerminLabelEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use TerminLabelEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\TerminLabelEndpointTrait
 */
final class TerminLabelEndpointTraitTest extends UnitTestCase {
    public function testTerminLabelEndpointTrait(): void {
        $endpoint = new TerminLabelEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'details',
            'fileIds',
            'icon',
            'ident',
            'imageIds',
            'name',
            'position',
        ], $keys);
    }
}
