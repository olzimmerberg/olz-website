<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Quiz\Endpoints;

use Olz\Apps\Quiz\Endpoints\GetMySkillLevelsEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Quiz\Endpoints\GetMySkillLevelsEndpoint
 */
final class GetMySkillLevelsEndpointTest extends UnitTestCase {
    public function testGetMySkillLevelsEndpointNotAnyPermission(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query['any'] = false;
        $endpoint = new GetMySkillLevelsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'skillFilter' => null,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetMySkillLevelsEndpointAll(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query['any'] = true;
        $endpoint = new GetMySkillLevelsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'skillFilter' => null,
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'Skill:1' => ['value' => 0.5],
            'Skill:2' => ['value' => 0.25],
            'Skill:3' => ['value' => 0.0],
        ], $result);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testGetMySkillLevelsEndpointCategoryIdIn(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query['any'] = true;
        $endpoint = new GetMySkillLevelsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'skillFilter' => [
                'categoryIdIn' => ['SkillCategory:1', 'SkillCategory:2'],
            ],
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'Skill:4' => ['value' => 0.75],
            'Skill:5' => ['value' => 0.0],
        ], $result);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
