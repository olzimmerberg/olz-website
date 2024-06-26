<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Quiz;

use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<SkillLevel>
 */
class FakeSkillLevelRepository extends FakeOlzRepository {
    public string $olzEntityClass = SkillLevel::class;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria['skill'] === 1) {
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

    /** @return array<SkillLevel> */
    public function getSkillLevelsForUserId(int $user_id): array {
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

    /**
     * @param array<int> $category_ids
     *
     * @return array<SkillLevel>
     */
    public function getSkillLevelsForUserIdInCategories(int $user_id, array $category_ids): array {
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
        throw new \Exception("Not mocked: {$category_ids_json}");
    }
}
