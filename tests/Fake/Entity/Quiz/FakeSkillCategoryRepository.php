<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Quiz;

use Olz\Entity\Quiz\SkillCategory;

class FakeSkillCategoryRepository {
    public function findOneBy($where) {
        if ($where === ['name' => 'Child Category 1']) {
            $skill_category = new SkillCategory();
            $skill_category->setId(11);
            return $skill_category;
        }
        $id = $where['id'] ?? null;
        if ($id && $id <= 3) {
            $skill_category = new SkillCategory();
            $skill_category->setId($id);
            return $skill_category;
        }
        return null;
    }
}
