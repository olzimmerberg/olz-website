<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\AddUserRoleMembershipEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\AddUserRoleMembershipEndpoint
 */
final class AddUserRoleMembershipEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'ids' => [
                'roleId' => FakeRole::adminRole()->getId(),
                'userId' => FakeUser::vorstandUser()->getId(),
            ],
        ];
    }

    public function testAddUserRoleMembershipEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new AddUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame([
                [FakeRole::adminRole(), null, null, null, null, 'roles'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testAddUserRoleMembershipEndpointInexistentRole(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new AddUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...$this->getValidInput(),
                'ids' => [
                    ...$this->getValidInput()['ids'],
                    'roleId' => FakeOlzRepository::NULL_ID,
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testAddUserRoleMembershipEndpointInexistentUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new AddUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...$this->getValidInput(),
                'ids' => [
                    ...$this->getValidInput()['ids'],
                    'userId' => FakeOlzRepository::NULL_ID,
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls
            );
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testAddUserRoleMembershipEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new AddUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call($this->getValidInput());

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([], $result);

        $this->assertSame([
            [FakeRole::adminRole(), null, null, null, null, 'roles'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(2, $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);

        $role = $entity_manager->persisted[0];
        $this->assertSame(FakeRole::adminRole(), $role);
        $this->assertSame([
            FakeUser::adminUser()->getId(),
            FakeUser::vorstandUser()->getId(),
        ], array_map(function ($item) {
            return $item->getId();
        }, [...$role->getUsers()]));

        $user = $entity_manager->persisted[1];
        $this->assertSame(FakeUser::vorstandUser(), $user);
        $this->assertSame([
            FakeRole::vorstandRole()->getId(),
            FakeRole::adminRole()->getId(),
        ], array_map(function ($item) {
            return $item->getId();
        }, [...$user->getRoles()]));
    }
}
