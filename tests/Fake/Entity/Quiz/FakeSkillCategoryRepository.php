<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Quiz;

use Olz\Entity\Quiz\SkillCategory;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeSkillCategoryRepository extends FakeOlzRepository {
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria === ['name' => 'Child Category 1']) {
            $skill_category = new SkillCategory();
            $skill_category->setId(11);
            return $skill_category;
        }
        $id = $criteria['id'] ?? null;
        if ($id && $id <= 3) {
            $skill_category = new SkillCategory();
            $skill_category->setId($id);
            return $skill_category;
        }
        return null;
    }
}
