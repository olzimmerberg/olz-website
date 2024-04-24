<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Service\Endpoints\DownloadEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class DownloadEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use DownloadEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\DownloadEndpointTrait
 */
final class DownloadEndpointTraitTest extends UnitTestCase {
    public function testDownloadEndpointTrait(): void {
        $endpoint = new DownloadEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'fileId',
            'name',
            'position',
        ], $keys);
    }
}
