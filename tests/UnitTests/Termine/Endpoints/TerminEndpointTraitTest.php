<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Termine\Endpoints\TerminEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class TerminEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use TerminEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\TerminEndpointTrait
 */
final class TerminEndpointTraitTest extends UnitTestCase {
    public function testTerminEndpointTrait(): void {
        $endpoint = new TerminEndpointTraitConcreteEndpoint();
        $this->assertFalse($endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertTrue($field instanceof FieldTypes\ObjectField);
        $this->assertFalse($field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'coordinateX',
            'coordinateY',
            'deadline',
            'endDate',
            'endTime',
            'fileIds',
            'go2olId',
            'imageIds',
            'locationId',
            'newsletter',
            'solvId',
            'startDate',
            'startTime',
            'text',
            'title',
            'types',
        ], $keys);
    }
}
