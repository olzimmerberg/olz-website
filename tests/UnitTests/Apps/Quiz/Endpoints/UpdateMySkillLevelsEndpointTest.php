<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Quiz\Endpoints;

use Olz\Apps\Quiz\Endpoints\UpdateMySkillLevelsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Quiz\Endpoints\UpdateMySkillLevelsEndpoint
 */
final class UpdateMySkillLevelsEndpointTest extends UnitTestCase {
    public function testUpdateMySkillLevelsEndpointNotAnyPermission(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query['any'] = false;
        $endpoint = new UpdateMySkillLevelsEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'updates' => [],
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

    public function testUpdateMySkillLevelsEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query['any'] = true;
        $endpoint = new UpdateMySkillLevelsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'updates' => [
                'Skill:1' => [
                    'change' => 0.5,
                ],
                'Skill:2' => [
                    'change' => -0.1,
                ],
            ],
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 2, 1, 0.0],
        ], array_map(
            function ($skill_level) {
                return [
                    $skill_level->getId(),
                    $skill_level->getSkill()->getId(),
                    $skill_level->getUser()->getId(),
                    $skill_level->getValue(),
                ];
            },
            $entity_manager->persisted,
        ));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
