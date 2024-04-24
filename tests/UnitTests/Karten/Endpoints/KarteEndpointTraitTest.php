<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Karten\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Karten\Endpoints\KarteEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class KarteEndpointTraitConcreteEndpoint extends OlzEntityEndpoint {
    use KarteEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Karten\Endpoints\KarteEndpointTrait
 */
final class KarteEndpointTraitTest extends UnitTestCase {
    public function testKarteEndpointTrait(): void {
        $endpoint = new KarteEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'centerX',
            'centerY',
            'kartennr',
            'kind',
            'name',
            'place',
            'previewImageId',
            'scale',
            'year',
            'zoom',
        ], $keys);
    }
}
