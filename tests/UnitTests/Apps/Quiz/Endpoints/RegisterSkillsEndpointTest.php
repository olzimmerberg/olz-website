<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Quiz\Endpoints;

use Olz\Apps\Quiz\Endpoints\RegisterSkillsEndpoint;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

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
 *
 * @covers \Olz\Apps\Quiz\Endpoints\RegisterSkillsEndpoint
 */
final class RegisterSkillsEndpointTest extends UnitTestCase {
    public function testRegisterSkillsEndpointIdent(): void {
        $endpoint = new RegisterSkillsEndpoint();
        $this->assertSame('RegisterSkillsEndpoint', $endpoint->getIdent());
    }

    public function testRegisterSkillsEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $skill_category_repo = new FakeRegisterSkillsEndpointSkillCategoryRepository();
        $entity_manager->repositories[SkillCategory::class] = $skill_category_repo;
        $skill_repo = new FakeRegisterSkillsEndpointSkillRepository();
        $entity_manager->repositories[Skill::class] = $skill_repo;
        $endpoint = new RegisterSkillsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'skills' => [
                [
                    'name' => 'Child Category 1 Skill',
                    'categoryIds' => ['SkillCategory:2'],
                ],
                [
                    'name' => 'Multi Category Skill',
                    'categoryIds' => ['SkillCategory:1', 'SkillCategory:3'],
                ],
                [
                    'name' => 'Parent Category Skill',
                    'categoryIds' => ['SkillCategory:1'],
                ],
            ],
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'idByName' => [
                'Child Category 1 Skill' => 'Skill:11',
                'Multi Category Skill' => 'Skill:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
                'Parent Category Skill' => 'Skill:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
            ],
        ], $result);

        $this->assertSame([
            [11, 'Child Category 1 Skill', [2]],
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 'Multi Category Skill', [1, 3]],
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 'Parent Category Skill', [1]],
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
