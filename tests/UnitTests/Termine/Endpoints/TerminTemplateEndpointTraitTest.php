<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Termine\Endpoints\TerminTemplateEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class TerminTemplateEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use TerminTemplateEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\TerminTemplateEndpointTrait
 */
final class TerminTemplateEndpointTraitTest extends UnitTestCase {
    public function testTerminTemplateEndpointTrait(): void {
        $endpoint = new TerminTemplateEndpointTraitConcreteEndpoint();
        $this->assertFalse($endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertTrue($field instanceof FieldTypes\ObjectField);
        $this->assertFalse($field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'deadlineEarlierSeconds',
            'deadlineTime',
            'durationSeconds',
            'fileIds',
            'imageIds',
            'locationId',
            'newsletter',
            'shouldPromote',
            'startTime',
            'text',
            'title',
            'types',
        ], $keys);
    }
}
