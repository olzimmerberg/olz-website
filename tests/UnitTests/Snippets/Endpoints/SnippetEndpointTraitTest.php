<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Snippets\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Snippets\Endpoints\SnippetEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class SnippetEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use SnippetEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Snippets\Endpoints\SnippetEndpointTrait
 */
final class SnippetEndpointTraitTest extends UnitTestCase {
    public function testSnippetEndpointTrait(): void {
        $endpoint = new SnippetEndpointTraitConcreteEndpoint();
        $this->assertFalse($endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertTrue($field instanceof FieldTypes\ObjectField);
        $this->assertFalse($field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'fileIds',
            'imageIds',
            'text',
        ], $keys);
    }
}
