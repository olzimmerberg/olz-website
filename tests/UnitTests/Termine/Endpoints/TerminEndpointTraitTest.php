<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\TerminEndpointTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class TerminEndpointTraitConcreteEndpoint {
    use TerminEndpointTrait;

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
 * @covers \Olz\Termine\Endpoints\TerminEndpointTrait
 */
final class TerminEndpointTraitTest extends UnitTestCase {
    public function testTerminEndpointTrait(): void {
        $endpoint = new TerminEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
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
            'link',
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
