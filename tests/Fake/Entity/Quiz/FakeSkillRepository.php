<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Quiz;

use Olz\Entity\Quiz\Skill;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeSkillRepository extends FakeOlzRepository {
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria === ['name' => 'Child Category 1 Skill']) {
            $skill = new Skill();
            $skill->setId(11);
            return $skill;
        }
        if ($criteria === ['id' => 2]) {
            $skill_2 = new Skill();
            $skill_2->setId(2);
            return $skill_2;
        }
        return null;
    }

    public function findAll(): array {
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
        throw new \Exception("Not mocked: {$category_ids_json}");
    }
}
