<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../public/_/quiz/endpoints/RegisterSkillCategoriesEndpoint.php';
require_once __DIR__.'/../../../../public/_/config/vendor/autoload.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEntityUtils.php';
require_once __DIR__.'/../../../fake/FakeIdUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeRegisterSkillCategoriesEndpointSkillCategoryRepository {
    public function findOneBy($where) {
        if ($where === ['name' => 'Child Category 1']) {
            $skill_category = new SkillCategory();
            $skill_category->setId(11);
            return $skill_category;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \RegisterSkillCategoriesEndpoint
 */
final class RegisterSkillCategoriesEndpointTest extends UnitTestCase {
    public function testRegisterSkillCategoriesEndpointIdent(): void {
        $endpoint = new RegisterSkillCategoriesEndpoint();
        $this->assertSame('RegisterSkillCategoriesEndpoint', $endpoint->getIdent());
    }

    public function testRegisterSkillCategoriesEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $skill_category_repo = new FakeRegisterSkillCategoriesEndpointSkillCategoryRepository();
        $entity_manager->repositories['SkillCategory'] = $skill_category_repo;
        $entity_utils = new FakeEntityUtils();
        $logger = new Logger('RegisterSkillCategoriesEndpointTest');
        $endpoint = new RegisterSkillCategoriesEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setIdUtils(new FakeIdUtils());
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'skillCategories' => [
                [
                    'name' => 'Child Category 1',
                    'parentCategoryName' => 'Parent Category',
                ],
                [
                    'name' => 'Child Category 2',
                    'parentCategoryName' => 'Parent Category',
                ],
                [
                    'name' => 'Parent Category',
                    'parentCategoryName' => null,
                ],
            ],
        ]);

        $this->assertSame([
            'idByName' => [
                'Child Category 1' => 'SkillCategory:11',
                'Child Category 2' => 'SkillCategory:'.FakeEntityManager::AUTO_INCREMENT_ID,
                'Parent Category' => 'SkillCategory:'.FakeEntityManager::AUTO_INCREMENT_ID,
            ],
        ], $result);

        $this->assertSame([
            [11, 'Child Category 1', 'Parent Category'],
            [FakeEntityManager::AUTO_INCREMENT_ID, 'Child Category 2', 'Parent Category'],
            [FakeEntityManager::AUTO_INCREMENT_ID, 'Parent Category', null],
        ], array_map(
            function ($skill_category) {
                $parent_category = $skill_category->getParentCategory();
                return [
                    $skill_category->getId(),
                    $skill_category->getName(),
                    $parent_category ? $parent_category->getName() : null,
                ];
            },
            $entity_manager->persisted,
        ));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
