<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/quiz/endpoints/RegisterSkillsEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/IdUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEntityUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeRegisterSkillsEndpointSkillCategoryRepository {
    public function findOneBy($where) {
        $id = $where['id'] ?? null;
        if ($id && $id <= 3) {
            $skill_category = new SkillCategory();
            $skill_category->setId($id);
            return $skill_category;
        }
        return null;
    }
}

class FakeRegisterSkillsEndpointSkillRepository {
    public function findOneBy($where) {
        if ($where === ['name' => 'Child Category 1 Skill']) {
            $skill = new Skill();
            $skill->setId(11);
            return $skill;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \RegisterSkillsEndpoint
 */
final class RegisterSkillsEndpointTest extends UnitTestCase {
    public function testRegisterSkillsEndpointIdent(): void {
        $endpoint = new RegisterSkillsEndpoint();
        $this->assertSame('RegisterSkillsEndpoint', $endpoint->getIdent());
    }

    public function testRegisterSkillsEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $skill_category_repo = new FakeRegisterSkillsEndpointSkillCategoryRepository();
        $entity_manager->repositories['SkillCategory'] = $skill_category_repo;
        $skill_repo = new FakeRegisterSkillsEndpointSkillRepository();
        $entity_manager->repositories['Skill'] = $skill_repo;
        $entity_utils = new FakeEntityUtils();
        $id_utils = new IdUtils();
        $logger = new Logger('RegisterSkillsEndpointTest');
        $endpoint = new RegisterSkillsEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setIdUtils($id_utils);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'skills' => [
                [
                    'name' => 'Child Category 1 Skill',
                    'categoryIds' => [
                        $id_utils->toExternalId(2, 'SkillCategory'),
                    ],
                ],
                [
                    'name' => 'Multi Category Skill',
                    'categoryIds' => [
                        $id_utils->toExternalId(1, 'SkillCategory'),
                        $id_utils->toExternalId(3, 'SkillCategory'),
                    ],
                ],
                [
                    'name' => 'Parent Category Skill',
                    'categoryIds' => [
                        $id_utils->toExternalId(1, 'SkillCategory'),
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            'idByName' => [
                'Child Category 1 Skill' => 'MTEtbXdHRU1R',
                'Multi Category Skill' => 'MjcwLW13R0VNUQ',
                'Parent Category Skill' => 'MjcwLW13R0VNUQ',
            ],
        ], $result);

        $this->assertSame([
            [11, 'Child Category 1 Skill', [2]],
            [FakeEntityManager::AUTO_INCREMENT_ID, 'Multi Category Skill', [1, 3]],
            [FakeEntityManager::AUTO_INCREMENT_ID, 'Parent Category Skill', [1]],
        ], array_map(
            function ($skill) {
                $categories = [];
                foreach ($skill->getCategories() as $category) {
                    $categories[] = $category->getId();
                }
                return [
                    $skill->getId(),
                    $skill->getName(),
                    $categories,
                ];
            },
            $entity_manager->persisted,
        ));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
