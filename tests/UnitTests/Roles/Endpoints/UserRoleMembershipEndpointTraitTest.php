<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\UserRoleMembershipEndpointTrait;
use Olz\Tests\UnitTests\Common\TraitConcreteEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\Fields\FieldTypes;

class UserRoleMembershipEndpointTraitConcreteEndpoint {
    use UserRoleMembershipEndpointTrait;
    use TraitConcreteEndpoint;
}

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\UserRoleMembershipEndpointTrait
 */
final class UserRoleMembershipEndpointTraitTest extends UnitTestCase {
    public function testUserRoleMembershipEndpointTrait(): void {
        $endpoint = new UserRoleMembershipEndpointTraitConcreteEndpoint();

        $field = $endpoint->getIdsField();
        $this->assertTrue($field instanceof FieldTypes\ObjectField);
        $this->assertFalse($field->getAllowNull());
        $field_structure = $field->getFieldStructure();
        $keys = array_keys($field_structure);
        sort($keys);
        $this->assertSame([
            'roleId',
            'userId',
        ], $keys);
    }
}
