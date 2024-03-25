<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Snippets\Endpoints;

use Olz\Snippets\Endpoints\SnippetEndpointTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class SnippetEndpointTraitConcreteEndpoint {
    use SnippetEndpointTrait;

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
 * @covers \Olz\Snippets\Endpoints\SnippetEndpointTrait
 */
final class SnippetEndpointTraitTest extends UnitTestCase {
    public function testSnippetEndpointTrait(): void {
        $endpoint = new SnippetEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
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
