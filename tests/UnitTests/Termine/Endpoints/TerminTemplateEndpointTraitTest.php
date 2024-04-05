<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\TerminTemplateEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class TerminTemplateEndpointTraitConcreteEndpoint {
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
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'deadlineEarlierSeconds',
            'deadlineTime',
            'durationSeconds',
            'fileIds',
            'imageIds',
            'link',
            'locationId',
            'newsletter',
            'startTime',
            'text',
            'title',
            'types',
        ], $keys);
    }
}
