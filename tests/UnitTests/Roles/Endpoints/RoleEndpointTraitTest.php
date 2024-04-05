<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\RoleEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class RoleEndpointTraitConcreteEndpoint {
    use RoleEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\RoleEndpointTrait
 */
final class RoleEndpointTraitTest extends UnitTestCase {
    public function testRoleEndpointTrait(): void {
        $endpoint = new RoleEndpointTraitConcreteEndpoint();
        $this->assertSame(false, $endpoint->usesExternalId());

        $field = $endpoint->getEntityDataField(/* allow_null= */ false);
        $this->assertSame(true, $field instanceof FieldTypes\ObjectField);
        $this->assertSame(false, $field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'canHaveChildRoles',
            'description',
            'featuredIndex',
            'fileIds',
            'guide',
            'imageIds',
            'indexWithinParent',
            'name',
            'parentRole',
            'title',
            'username',
        ], $keys);
    }
}
