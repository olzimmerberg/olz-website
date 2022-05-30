<?php

declare(strict_types=1);

use Monolog\Logger;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../_/quiz/endpoints/GetMySkillLevelsEndpoint.php';
require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeIdUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeGetMySkillLevelsEndpointSkillRepository {
    public function findAll() {
        $skill_1 = new Skill();
        $skill_1->setId(1);
        $skill_2 = new Skill();
        $skill_2->setId(2);
        $skill_3 = new Skill();
        $skill_3->setId(3);
        return [$skill_1, $skill_2, $skill_3];
    }

    public function getSkillsInCategories($category_ids) {
        if ($category_ids === [1, 2]) {
            $skill_4 = new Skill();
            $skill_4->setId(4);
            $skill_5 = new Skill();
            $skill_5->setId(5);
            return [$skill_4, $skill_5];
        }
        $category_ids_json = json_encode($category_ids);
        throw new Exception("Not mocked: {$category_ids_json}");
    }
}

class FakeGetMySkillLevelsEndpointSkillLevelRepository {
    public function getSkillLevelsForUserId($user_id) {
        $skill_1 = new Skill();
        $skill_1->setId(1);
        $skill_2 = new Skill();
        $skill_2->setId(2);
        $skill_level_1 = new SkillLevel();
        $skill_level_1->setId(1);
        $skill_level_1->setSkill($skill_1);
        $skill_level_1->setValue(0.5);
        $skill_level_2 = new SkillLevel();
        $skill_level_2->setId(2);
        $skill_level_2->setSkill($skill_2);
        $skill_level_2->setValue(0.25);
        return [$skill_level_1, $skill_level_2];
    }

    public function getSkillLevelsForUserIdInCategories($user_id, $category_ids) {
        if ($category_ids === [1, 2]) {
            $skill_4 = new Skill();
            $skill_4->setId(4);
            $skill_level_4 = new SkillLevel();
            $skill_level_4->setId(4);
            $skill_level_4->setSkill($skill_4);
            $skill_level_4->setValue(0.75);
            return [$skill_level_4];
        }
        $category_ids_json = json_encode($category_ids);
        throw new Exception("Not mocked: {$category_ids_json}");
    }
}

/**
 * @internal
 * @covers \GetMySkillLevelsEndpoint
 */
final class GetMySkillLevelsEndpointTest extends UnitTestCase {
    public function testGetMySkillLevelsEndpointIdent(): void {
        $endpoint = new GetMySkillLevelsEndpoint();
        $this->assertSame('GetMySkillLevelsEndpoint', $endpoint->getIdent());
    }

    public function testGetMySkillLevelsEndpointNotAnyPermission(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['any'] = false;
        $logger = new Logger('GetMySkillLevelsEndpointTest');
        $endpoint = new GetMySkillLevelsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLogger($logger);

        try {
            $endpoint->call([
                'skillFilter' => null,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetMySkillLevelsEndpointAll(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['any'] = true;
        $entity_manager = new FakeEntityManager();
        $skill_repo = new FakeGetMySkillLevelsEndpointSkillRepository();
        $entity_manager->repositories['Skill'] = $skill_repo;
        $skill_level_repo = new FakeGetMySkillLevelsEndpointSkillLevelRepository();
        $entity_manager->repositories['SkillLevel'] = $skill_level_repo;
        $logger = new Logger('GetMySkillLevelsEndpointTest');
        $endpoint = new GetMySkillLevelsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setIdUtils(new FakeIdUtils());
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'skillFilter' => null,
        ]);

        $this->assertSame([
            'Skill:1' => ['value' => 0.5],
            'Skill:2' => ['value' => 0.25],
            'Skill:3' => ['value' => 0],
        ], $result);

        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }

    public function testGetMySkillLevelsEndpointCategoryIdIn(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query['any'] = true;
        $entity_manager = new FakeEntityManager();
        $skill_repo = new FakeGetMySkillLevelsEndpointSkillRepository();
        $entity_manager->repositories['Skill'] = $skill_repo;
        $skill_level_repo = new FakeGetMySkillLevelsEndpointSkillLevelRepository();
        $entity_manager->repositories['SkillLevel'] = $skill_level_repo;
        $logger = new Logger('GetMySkillLevelsEndpointTest');
        $endpoint = new GetMySkillLevelsEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setIdUtils(new FakeIdUtils());
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'skillFilter' => [
                'categoryIdIn' => ['SkillCategory:1', 'SkillCategory:2'],
            ],
        ]);

        $this->assertSame([
            'Skill:4' => ['value' => 0.75],
            'Skill:5' => ['value' => 0],
        ], $result);

        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
