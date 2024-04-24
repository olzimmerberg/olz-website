<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\News\Endpoints\NewsEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class NewsEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use NewsEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\NewsEndpointTrait
 */
final class NewsEndpointTraitTest extends UnitTestCase {
    public function testNewsEndpointTrait(): void {
        $endpoint = new NewsEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'authorEmail',
            'authorName',
            'authorRoleId',
            'authorUserId',
            'content',
            'externalUrl',
            'fileIds',
            'format',
            'imageIds',
            'publishAt',
            'tags',
            'teaser',
            'terminId',
            'title',
        ], $keys);
    }
}
