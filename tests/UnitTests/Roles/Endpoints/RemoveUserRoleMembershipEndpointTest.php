<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\RemoveUserRoleMembershipEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\RemoveUserRoleMembershipEndpoint
 */
final class RemoveUserRoleMembershipEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'ids' => [
                'roleId' => FakeRole::adminRole()->getId(),
                'userId' => FakeUser::adminUser()->getId(),
            ],
        ];
    }

    public function testRemoveUserRoleMembershipEndpointIdent(): void {
        $endpoint = new RemoveUserRoleMembershipEndpoint();
        $this->assertSame('RemoveUserRoleMembershipEndpoint', $endpoint->getIdent());
    }

    public function testRemoveUserRoleMembershipEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new RemoveUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testRemoveUserRoleMembershipEndpointInexistentRole(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new RemoveUserRoleMembershipEndpoint();
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
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testRemoveUserRoleMembershipEndpointInexistentUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new RemoveUserRoleMembershipEndpoint();
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
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testRemoveUserRoleMembershipEndpointInexistentMembership(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new RemoveUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            ...$this->getValidInput(),
            'ids' => [
                ...$this->getValidInput()['ids'],
                'userId' => FakeUser::vorstandUser()->getId(),
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(2, $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);

        $role = $entity_manager->persisted[0];
        $this->assertSame(FakeRole::adminRole(), $role);
        $this->assertSame([
            FakeUser::adminUser()->getId(),
        ], array_map(function ($item) {
            return $item->getId();
        }, [...$role->getUsers()]));

        $user = $entity_manager->persisted[1];
        $this->assertSame(FakeUser::vorstandUser(), $user);
        $this->assertSame([
            FakeRole::vorstandRole()->getId(),
        ], array_map(function ($item) {
            return $item->getId();
        }, [...$user->getRoles()]));
    }

    public function testRemoveUserRoleMembershipEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new RemoveUserRoleMembershipEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call($this->getValidInput());

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame(['status' => 'OK'], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(2, $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);

        $role = $entity_manager->persisted[0];
        $this->assertSame(FakeRole::adminRole(), $role);
        $this->assertSame([], array_map(function ($item) {
            return $item->getId();
        }, [...$role->getUsers()]));

        $user = $entity_manager->persisted[1];
        $this->assertSame(FakeUser::adminUser(), $user);
        $this->assertSame([], array_map(function ($item) {
            return $item->getId();
        }, [...$user->getRoles()]));
    }
}
