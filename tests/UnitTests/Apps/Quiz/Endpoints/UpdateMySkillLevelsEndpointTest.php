<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Quiz\Endpoints;

use Olz\Apps\Quiz\Endpoints\UpdateMySkillLevelsEndpoint;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use PhpTypeScriptApi\HttpError;

class FakeUpdateMySkillLevelsEndpointSkillRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 2]) {
            $skill_2 = new Skill();
            $skill_2->setId(2);
            return $skill_2;
        }
        return null;
    }
}

class FakeUpdateMySkillLevelsEndpointSkillLevelRepository {
    public function findOneBy($where) {
        if ($where['skill'] === 1) {
            $skill_1 = new Skill();
            $skill_1->setId(1);
            $skill_level_1 = new SkillLevel();
            $skill_level_1->setId(1);
            $skill_level_1->setSkill($skill_1);
            $skill_level_1->setValue(0.5);
            return $skill_level_1;
        }
        return null;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Quiz\Endpoints\UpdateMySkillLevelsEndpoint
 */
final class UpdateMySkillLevelsEndpointTest extends UnitTestCase {
    public function testUpdateMySkillLevelsEndpointIdent(): void {
        $endpoint = new UpdateMySkillLevelsEndpoint();
        $this->assertSame('UpdateMySkillLevelsEndpoint', $endpoint->getIdent());
    }

    public function testUpdateMySkillLevelsEndpointNotAnyPermission(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query['any'] = false;
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateMySkillLevelsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'updates' => [],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateMySkillLevelsEndpoint(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query['any'] = true;
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new Fake\FakeEntityManager();
        $skill_repo = new FakeUpdateMySkillLevelsEndpointSkillRepository();
        $entity_manager->repositories[Skill::class] = $skill_repo;
        $skill_level_repo = new FakeUpdateMySkillLevelsEndpointSkillLevelRepository();
        $entity_manager->repositories[SkillLevel::class] = $skill_level_repo;
        $entity_utils = new Fake\FakeEntityUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateMySkillLevelsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils($date_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setIdUtils(new Fake\FakeIdUtils());
        $endpoint->setLog($logger);

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
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);

        $this->assertSame([
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 2, 2, 0],
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
